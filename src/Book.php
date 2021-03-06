<?php

    class Book

    {
        private $title;
        private $id;

        function __construct($title, $id = null)
        {
            $this->title = $title;
            $this->id = $id;
        }

        function setTitle($new_title)
        {
            $this->title = (string) $new_title;
        }

        function getTitle()
        {
            return $this->title;
        }

        function getId()
        {
            return $this->id;
        }

        function save()
        {
            $query=$GLOBALS['DB']->query("SELECT * FROM books WHERE title = '{$this->getTitle()}';");
            $returned_book = $query->fetchAll(PDO::FETCH_ASSOC);
            $found_book = null;

            foreach($returned_book as $book) {
              $book_id = $book['id'];
              $found_book = Book::find($book_id);
            }

            if ($found_book != null) {
              return $found_book;
            }
            else {
              $GLOBALS['DB']->exec("INSERT INTO books (title) VALUES ('{$this->getTitle()}');");
              $this->id = $GLOBALS['DB']->lastInsertId();
            }
        }

        static function getAll()
        {
            $returned_books = $GLOBALS['DB']->query("SELECT * FROM books;");
            $books = array();
            foreach($returned_books as $book) {
                $title = $book['title'];
                $id = $book['id'];
                $new_book = new Book($title, $id);
                array_push($books, $new_book);
            }
            return $books;
        }

        static function deleteAll()
        {
            $GLOBALS['DB']->exec("DELETE FROM books");
        }

        static function find($id)
        {
            $all_books = Book::getAll();
            $found_book = null;
            foreach ($all_books as $book) {
                $book_id = $book->getId();
                if ($book_id == $id) {
                    $found_book = $book;
                }
            }
            return $found_book;
        }

        function update($new_title)
        {
            $GLOBALS['DB']->exec("UPDATE books SET title = '{$new_title}' WHERE id = {$this->getId()};");
            $this->setTitle($new_title);
        }

        function delete()
        {
            $GLOBALS['DB']->exec("DELETE FROM books WHERE id = {$this->getId()};");
        }

        static function search($search_title)
        {
            $all_books = Book::getAll();
            $lowercase_search = strtolower($search_title);
            $found_books = array();
            foreach($all_books as $book) {
                $lowercase_book = strtolower($book->getTitle());
                $compare = strpos($lowercase_book, $lowercase_search);
                if( is_numeric($compare)) {
                    array_push($found_books, $book);
                }
            }
            return $found_books;
        }

        function addAuthor($author)
        {
            $GLOBALS['DB']->exec("INSERT INTO books_authors (books_id, authors_id) VALUES ({$this->getId()}, {$author->getId()});");
        }

        function getAuthors()
        {
            $query = $GLOBALS['DB']->query("SELECT authors.* FROM books JOIN books_authors ON (books.id = books_authors.books_id) JOIN authors ON (books_authors.authors_id = authors.id) WHERE books.id = {$this->getId()};");
            $returned_authors = $query->fetchAll(PDO::FETCH_ASSOC);
            $authors = array();
            foreach($returned_authors as $author) {
                $name = $author['name'];
                $id = $author['id'];
                $new_author = new Author($name, $id);
                array_push($authors, $new_author);
            }
            return $authors;
        }






    }

?>
