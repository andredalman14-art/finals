<?php
session_start();

function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function current_user_name()
{
    return isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Librarian';
}

function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

function render_nav($active_page = '')
{
    if (is_logged_in()) {
        echo '<a' . ($active_page == 'dashboard' ? ' class="active"' : '') . ' href="dashboard.php">Dashboard</a>';
        echo '<a' . ($active_page == 'books' ? ' class="active"' : '') . ' href="book_list.php">Books</a>';
        echo '<a' . ($active_page == 'borrowers' ? ' class="active"' : '') . ' href="borrowers.php">Borrowers</a>';
        echo '<a' . ($active_page == 'add_book' ? ' class="active"' : '') . ' href="add_book.php">Add Book</a>';
        echo '<a' . ($active_page == 'status' ? ' class="active"' : '') . ' href="status.php">Status</a>';
        echo '<a' . ($active_page == 'profile' ? ' class="active"' : '') . ' href="profile.php">Profile</a>';
        echo '<a class="nav-button" href="logout.php">Logout</a>';
    } else {
        echo '<a href="../index.html">Home</a>';
        echo '<a' . ($active_page == 'dashboard' ? ' class="active"' : '') . ' href="dashboard.php">Dashboard</a>';
        echo '<a' . ($active_page == 'status' ? ' class="active"' : '') . ' href="status.php">Status</a>';
        echo '<a class="nav-button' . ($active_page == 'login' ? ' active' : '') . '" href="login.php">Login</a>';
    }
}
?>
