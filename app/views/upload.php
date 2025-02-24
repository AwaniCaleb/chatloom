<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Content - My App</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <h1>Upload Content</h1>
    <form action="<?= BASE_URL ?>index.php?page=upload_process" method="post" enctype="multipart/form-data">
        <label for="file">Choose file (image or video):</label>
        <input type="file" name="file" id="file" required>
        <br>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
