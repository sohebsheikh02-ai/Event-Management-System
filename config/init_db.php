<?php

/**
 * Database Initializer
 *
 * Reads sql/event_management_schema.sql and applies it to the connected
 * database.  Safe to call on every request — all CREATE TABLE statements
 * are rewritten to CREATE TABLE IF NOT EXISTS, and duplicate-key errors
 * from the seed INSERTs are silently ignored.
 *
 * Requires: $conn (mysqli) to already be open.
 */

function init_database(mysqli $conn): void {
    $schema_file = __DIR__ . '/../sql/event_management_schema.sql';

    if (!file_exists($schema_file)) {
        error_log('init_db: schema file not found at ' . $schema_file);
        return;
    }

    $sql = file_get_contents($schema_file);
    if ($sql === false) {
        error_log('init_db: could not read schema file');
        return;
    }

    // Strip CREATE DATABASE / USE statements — Railway provides the database
    // and the connection is already scoped to the correct schema.
    $sql = preg_replace('/^\s*CREATE\s+DATABASE\b[^;]*;\s*/im', '', $sql);
    $sql = preg_replace('/^\s*USE\s+\w+\s*;\s*/im', '', $sql);

    // Make every CREATE TABLE idempotent.
    $sql = preg_replace('/\bCREATE\s+TABLE\s+(?!IF\s+NOT\s+EXISTS\s)/i', 'CREATE TABLE IF NOT EXISTS ', $sql);

    // Split on statement boundaries and execute one at a time so that a
    // duplicate-key error on a seed INSERT does not abort the whole batch.
    //
    // Use preg_split on semicolons that appear at the end of a line (optionally
    // followed by an inline comment) rather than a plain explode(';', ...).
    // A bare explode can leave multi-line INSERT blocks joined together when
    // the semicolon is not the very last character on its line, causing the
    // entire seed section to be submitted as a single query and bypassing the
    // per-statement error handler below.
    $statements = array_filter(
        array_map('trim', preg_split('/;\s*(?:--[^\n]*)?\n/u', $sql)),
        fn(string $s): bool => $s !== ''
    );

    foreach ($statements as $statement) {
        // Strip any trailing inline comment that survived the split (e.g. the
        // last statement in the file which has no newline after its semicolon).
        $statement = rtrim($statement, '; ');
        if ($statement === '') {
            continue;
        }

        if (!$conn->query($statement)) {
            $errno = $conn->errno;
            // 1062 = Duplicate entry (seed data already present) — safe to ignore.
            // 1050 = Table already exists (shouldn't happen with IF NOT EXISTS, but guard anyway).
            if (!in_array($errno, [1062, 1050], true)) {
                error_log('init_db: query failed (' . $errno . '): ' . $conn->error . ' — SQL: ' . substr($statement, 0, 200));
            }
        }
    }
}
