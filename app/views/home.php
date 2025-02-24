<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home - My App</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <header>
        <h1>Welcome to My App</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="<?= BASE_URL ?>index.php?page=logout">Logout</a>
            <a href="<?= BASE_URL ?>index.php?page=upload">Upload Content</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>index.php?page=login">Login</a>
        <?php endif; ?>
    </header>
    <main>
        <h2>Recent Uploads</h2>
        
    </main>
</body>
</html>
<!-- The home page displays a list of recent uploads. If the user is logged in, they can also log out or upload new content. If the user is not logged in, they can log in. The file information is retrieved from the database using the File model and displayed on the page. The file type is used to determine whether to display an image or video element for each file. If there are no uploads, a message is displayed indicating that there are no uploads yet. -->