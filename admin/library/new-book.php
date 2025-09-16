<?php include __DIR__ . '/../admin-checker.php'; ?>
<?php include __DIR__ . '/../../config/database.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="/config/styles.css">
    <link rel="icon" href="/assets/small icon.jpg" type="image/jpg">
    <link rel="stylesheet" href="/../config/styles.css">
    <style> .library-nav { background-color: #ddd; } </style>
</head>

<?php include __DIR__ . '/../adminheader.php'; ?>

<div class="page-content">
    <h1 class="centered">Book Adder</h1>
    <form action="add-book.php" method="POST" enctype="multipart/form-data" class="form-container" style="display: flex; flex-direction: column; align-items: center; gap: 10px; width: 75vw; margin: auto; font-size: 1.25em;">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="author">Author:</label>
        <input type="text" id="author" name="author" required>

        <label for="series">Series (if applicable):</label>
        <input type="text" id="series" name="series">

        <label for="book-number">Book Number (if applicable):</label>
        <input type="number" id="book-number" name="book-number">

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre">

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Read">Read</option>
            <option value="Reading">Reading</option>
            <option value="To Read">To Read</option>
            <option value="Abandoned">Abandoned</option>
        </select>

        <label for='rating'>Rating (1-5):</label>
        <input type='number' id='rating' name='rating' min='1' max='5'>

        <label for="image">Upload Image</label>
        <input type="file" id="image" name="image" accept="image/*">

        <button type="submit">Add Book</button>
</div>
