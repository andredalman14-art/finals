CREATE DATABASE IF NOT EXISTS shelfwise_library;
USE shelfwise_library;

DROP TABLE IF EXISTS borrow_transactions;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS books;
DROP TABLE IF EXISTS borrowers;
DROP TABLE IF EXISTS book_categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'Librarian',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE password_resets (
  reset_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token_hash CHAR(64) NOT NULL UNIQUE,
  expires_at DATETIME NOT NULL,
  used_at DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE book_categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE books (
  book_id INT AUTO_INCREMENT PRIMARY KEY,
  accession_number VARCHAR(50) NOT NULL UNIQUE,
  isbn VARCHAR(50),
  title VARCHAR(200) NOT NULL,
  author VARCHAR(150) NOT NULL,
  publisher VARCHAR(150),
  category_id INT,
  shelf_location VARCHAR(50),
  copies INT NOT NULL DEFAULT 1,
  status VARCHAR(50) NOT NULL DEFAULT 'Available',
  remarks TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES book_categories(category_id)
);

CREATE TABLE borrowers (
  borrower_id INT AUTO_INCREMENT PRIMARY KEY,
  borrower_code VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(150) NOT NULL,
  course_department VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  contact_number VARCHAR(30),
  account_status VARCHAR(50) NOT NULL DEFAULT 'Active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE borrow_transactions (
  transaction_id INT AUTO_INCREMENT PRIMARY KEY,
  borrower_id INT NOT NULL,
  book_id INT NOT NULL,
  date_borrowed DATE NOT NULL,
  due_date DATE NOT NULL,
  date_returned DATE,
  transaction_status VARCHAR(50) NOT NULL DEFAULT 'Borrowed',
  FOREIGN KEY (borrower_id) REFERENCES borrowers(borrower_id),
  FOREIGN KEY (book_id) REFERENCES books(book_id)
);

INSERT INTO users (full_name, email, password, role)
VALUES
('ShelfWise Admin', 'admin@shelfwise.edu', '$2y$10$7nkQH7aMvUHcr/i5Z/DmMeOKdQG25sTaU5e3nvYCwUK40bPJFkoP2', 'Administrator');

INSERT INTO book_categories (category_name) VALUES
('Computer Science'),
('Information Technology'),
('Literature'),
('Business'),
('General Reference');

INSERT INTO books
(accession_number, isbn, title, author, publisher, category_id, shelf_location, copies, status, remarks)
VALUES
('BK-1001', '978-971-100-100-1', 'Database Design for Beginners', 'L. Morales', 'Campus Press', 2, 'IT-A2', 3, 'Borrowed', 'Useful for database lessons.'),
('BK-1002', '978-971-100-100-2', 'Philippine Literature Today', 'C. Valdez', 'Isla Publishing', 3, 'LIT-C1', 2, 'Available', 'Second edition.'),
('BK-1003', '978-971-100-100-3', 'Modern Web Development', 'A. Tan', 'Code House', 1, 'CS-B5', 4, 'Overdue', 'High-demand book.'),
('BK-1004', '978-971-100-100-4', 'Introduction to Algorithms', 'R. Dela Cruz', 'Academic Works', 1, 'CS-A1', 2, 'Borrowed', 'Reference copy available.'),
('BK-1005', '978-971-100-100-5', 'Business Communication Essentials', 'M. Chua', 'Commerce Press', 4, 'BUS-D3', 5, 'Reserved', 'Reserved by faculty.');

INSERT INTO borrowers (borrower_code, full_name, course_department, email, contact_number, account_status)
VALUES
('BR-2026-001', 'Ana Reyes', 'BSIT 2A', 'ana.reyes@student.edu', '0918 234 1021', 'Active'),
('BR-2026-002', 'Marco Lim', 'BSCS 3B', 'marco.lim@student.edu', '0927 874 5510', 'Active'),
('BR-2026-003', 'Jessa Cruz', 'BSEd 1C', 'jessa.cruz@student.edu', '0916 445 7781', 'Active'),
('BR-2026-004', 'Prof. Daniel Uy', 'Faculty - IT Department', 'daniel.uy@faculty.edu', '0995 312 8104', 'Active');

INSERT INTO borrow_transactions (borrower_id, book_id, date_borrowed, due_date, date_returned, transaction_status)
VALUES
(1, 1, '2026-05-01', '2026-05-08', NULL, 'Borrowed'),
(2, 3, '2026-04-23', '2026-04-30', NULL, 'Overdue'),
(3, 4, '2026-05-03', '2026-05-10', NULL, 'Borrowed');
