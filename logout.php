<?php
/**
 * Logout Page
 * National High School Enrollment System
 */

session_start();
session_destroy();
header('Location: login.php');
exit();
?> 