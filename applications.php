<?php

require __DIR__ . '/includes/db.php';

$database = violets_database();
$selectedType = violets_text($_GET['type'] ?? '');

$sql = 'SELECT id, application_type, applicant_name, discord_handle, age, player_type, freestyle_type, rl_tracker_url, clips_link, software_used, portfolio_url, primary_platform, channel_url, followers_count, created_at FROM applications';
$params = [];

if ($selectedType !== '') {
    $sql .= ' WHERE application_type = :application_type';
    $params[':application_type'] = $selectedType;
}

$sql .= ' ORDER BY datetime(created_at) DESC, id DESC';

$statement = $database->prepare($sql);
$statement->execute($params);
$applications = $statement->fetchAll();
$totalCount = count($applications);

function field_value(array $row, string $key): string
{
    $value = $row[$key] ?? '';

    if ($value === null || $value === '') {
        return '—';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function type_label(string $type): string
{
    switch ($type) {
        case 'player':
            return 'Player';
        case 'designer':
            return 'Designer';
        case 'content':
            return 'Content Creator';
        default:
            return ucfirst($type);
    }
}

?><!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Violets | Applications</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <div class="video-overlay"></div>
    <main class="applications-page">
        <div class="applications-header">
            <div>
                <span class="applications-kicker">APPLICATIONS</span>
                <h1>طلبات التقديم</h1>
                <p>إجمالي الطلبات: <?php echo (int) $totalCount; ?></p>
            </div>
            <div class="applications-actions">
                <a class="applications-back" href="index.php">العودة للرئيسية</a>
                <a class="applications-back secondary" href="applications.php">عرض الكل</a>
            </div>
        </div>

        <div class="applications-filters">
            <a href="applications.php" class="filter-pill <?php echo $selectedType === '' ? 'active' : ''; ?>">الكل</a>
            <a href="applications.php?type=player" class="filter-pill <?php echo $selectedType === 'player' ? 'active' : ''; ?>">اللاعبين</a>
            <a href="applications.php?type=designer" class="filter-pill <?php echo $selectedType === 'designer' ? 'active' : ''; ?>">المصممين</a>
            <a href="applications.php?type=content" class="filter-pill <?php echo $selectedType === 'content' ? 'active' : ''; ?>">صناع المحتوى</a>
        </div>

        <section class="applications-panel">
            <?php if ($applications === []): ?>
                <div class="applications-empty">
                    <h2>لا توجد طلبات بعد</h2>
                    <p>عندما يتم إرسال الطلبات ستظهر هنا مباشرة.</p>
                </div>
            <?php else: ?>
                <div class="applications-table-wrap">
                    <table class="applications-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>النوع</th>
                                <th>الاسم</th>
                                <th>الديسكورد</th>
                                <th>العمر</th>
                                <th>Player Type</th>
                                <th>Freestyle</th>
                                <th>RL Tracker</th>
                                <th>Clips / Portfolio / Channel</th>
                                <th>Software / Platform</th>
                                <th>Followers</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $row): ?>
                                <tr>
                                    <td><?php echo (int) $row['id']; ?></td>
                                    <td><span class="type-badge type-<?php echo htmlspecialchars((string) $row['application_type'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars(type_label((string) $row['application_type']), ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><?php echo htmlspecialchars((string) $row['applicant_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $row['discord_handle'], ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo field_value($row, 'age'); ?></td>
                                    <td><?php echo field_value($row, 'player_type'); ?></td>
                                    <td><?php echo field_value($row, 'freestyle_type'); ?></td>
                                    <td><?php echo field_value($row, 'rl_tracker_url'); ?></td>
                                    <td>
                                        <?php
                                        $link = $row['clips_link'] ?: ($row['portfolio_url'] ?: $row['channel_url']);
                                        if ($link) {
                                            echo '<a href="' . htmlspecialchars((string) $link, ENT_QUOTES, 'UTF-8') . '" target="_blank" rel="noopener noreferrer">فتح الرابط</a>';
                                        } else {
                                            echo '—';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $extra = $row['software_used'] ?: $row['primary_platform'];
                                        echo $extra ? htmlspecialchars((string) $extra, ENT_QUOTES, 'UTF-8') : '—';
                                        ?>
                                    </td>
                                    <td><?php echo field_value($row, 'followers_count'); ?></td>
                                    <td><?php echo htmlspecialchars((string) $row['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
