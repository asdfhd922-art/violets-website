<?php

require __DIR__ . '/includes/db.php';

header('Content-Type: application/json; charset=utf-8');

$rawInput = file_get_contents('php://input');
$jsonInput = json_decode($rawInput, true);
$request = is_array($jsonInput) ? $jsonInput : $_POST;

function application_error(string $message, int $statusCode = 400): void
{
    http_response_code($statusCode);
    echo json_encode(['success' => false, 'error' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

$applicationType = violets_text($request['applicationType'] ?? '');

if ($applicationType === '') {
    application_error('applicationType is required');
}

$applicantName = violets_text($request['playerName'] ?? $request['designerName'] ?? $request['contentName'] ?? '');
$discordHandle = violets_text($request['discordHandle'] ?? '');

if ($applicantName === '') {
    application_error('name is required');
}

if ($discordHandle === '') {
    application_error('discordHandle is required');
}

$record = [
    ':application_type' => $applicationType,
    ':applicant_name' => $applicantName,
    ':discord_handle' => $discordHandle,
    ':age' => violets_number($request['age'] ?? null),
    ':player_type' => violets_text($request['playerType'] ?? '') ?: null,
    ':freestyle_type' => violets_text($request['freestyleType'] ?? '') ?: null,
    ':rl_tracker_url' => violets_text($request['rlTrackerUrl'] ?? '') ?: null,
    ':clips_link' => violets_text($request['clipsLink'] ?? '') ?: null,
    ':software_used' => violets_text($request['softwareUsed'] ?? '') ?: null,
    ':portfolio_url' => violets_text($request['portfolioUrl'] ?? '') ?: null,
    ':primary_platform' => violets_text($request['primaryPlatform'] ?? '') ?: null,
    ':channel_url' => violets_text($request['channelUrl'] ?? '') ?: null,
    ':followers_count' => violets_number($request['followersCount'] ?? null),
];

if ($applicationType === 'player') {
    if (($record[':clips_link'] ?? null) === null) {
        application_error('clipsLink is required for player applications');
    }

    if (($record[':player_type'] ?? null) === 'cch' && ($record[':rl_tracker_url'] ?? null) === null) {
        application_error('rlTrackerUrl is required for CCH applications');
    }
}

if ($applicationType === 'designer' && ($record[':portfolio_url'] ?? null) === null) {
    application_error('portfolioUrl is required for designer applications');
}

if ($applicationType === 'content' && ($record[':channel_url'] ?? null) === null) {
    application_error('channelUrl is required for content applications');
}

$database = violets_database();
$statement = $database->prepare(
    'INSERT INTO applications (
        application_type,
        applicant_name,
        discord_handle,
        age,
        player_type,
        freestyle_type,
        rl_tracker_url,
        clips_link,
        software_used,
        portfolio_url,
        primary_platform,
        channel_url,
        followers_count
    ) VALUES (
        :application_type,
        :applicant_name,
        :discord_handle,
        :age,
        :player_type,
        :freestyle_type,
        :rl_tracker_url,
        :clips_link,
        :software_used,
        :portfolio_url,
        :primary_platform,
        :channel_url,
        :followers_count
    )'
);

$statement->execute($record);

echo json_encode([
    'success' => true,
    'id' => (int) $database->lastInsertId(),
], JSON_UNESCAPED_UNICODE);
