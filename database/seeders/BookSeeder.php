<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'isbn' => '978-0-7432-7356-5', 'genre' => 'Fiction', 'publication_year' => 1925, 'publisher' => 'Scribner', 'pages' => 180, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A story of the fabulously wealthy Jay Gatsby and his love for the beautiful Daisy Buchanan.'],
            ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'isbn' => '978-0-06-112008-4', 'genre' => 'Fiction', 'publication_year' => 1960, 'publisher' => 'J. B. Lippincott & Co.', 'pages' => 281, 'total_copies' => 4, 'available_copies' => 4, 'description' => 'The unforgettable novel of a childhood in a sleepy Southern town and the crisis of conscience that rocked it.'],
            ['title' => '1984', 'author' => 'George Orwell', 'isbn' => '978-0-452-28423-4', 'genre' => 'Dystopia', 'publication_year' => 1949, 'publisher' => 'Secker & Warburg', 'pages' => 328, 'total_copies' => 5, 'available_copies' => 5, 'description' => 'A dystopian social science fiction novel that follows Winston Smith, a low-ranking member of \'the Party\'.'],
            ['title' => 'Pride and Prejudice', 'author' => 'Jane Austen', 'isbn' => '978-0-14-143951-8', 'genre' => 'Romance', 'publication_year' => 1813, 'publisher' => 'T. Egerton', 'pages' => 432, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'The story follows the main character Elizabeth Bennet as she deals with issues of manners, upbringing, morality, education, and marriage.'],
            ['title' => 'The Catcher in the Rye', 'author' => 'J.D. Salinger', 'isbn' => '978-0-316-76948-0', 'genre' => 'Fiction', 'publication_year' => 1951, 'publisher' => 'Little, Brown and Company', 'pages' => 277, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'The story of Holden Caulfield, a teenager from New York City who is expelled from prep school.'],
            ['title' => 'Brave New World', 'author' => 'Aldous Huxley', 'isbn' => '978-0-06-085052-4', 'genre' => 'Dystopia', 'publication_year' => 1932, 'publisher' => 'Chatto & Windus', 'pages' => 311, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'A dystopian social science fiction novel set in a futuristic World State.'],
            ['title' => 'The Alchemist', 'author' => 'Paulo Coelho', 'isbn' => '978-0-06-112241-5', 'genre' => 'Philosophy', 'publication_year' => 1988, 'publisher' => 'HarperOne', 'pages' => 197, 'total_copies' => 4, 'available_copies' => 4, 'description' => 'A philosophical novel about a young Andalusian shepherd on a journey to find treasure.'],
            ['title' => 'Harry Potter and the Sorcerer\'s Stone', 'author' => 'J.K. Rowling', 'isbn' => '978-0-590-35340-3', 'genre' => 'Fantasy', 'publication_year' => 1997, 'publisher' => 'Scholastic', 'pages' => 309, 'total_copies' => 6, 'available_copies' => 6, 'description' => 'The first novel in J.K. Rowling\'s Harry Potter series.'],
            ['title' => 'The Lord of the Rings', 'author' => 'J.R.R. Tolkien', 'isbn' => '978-0-618-57494-1', 'genre' => 'Fantasy', 'publication_year' => 1954, 'publisher' => 'Allen & Unwin', 'pages' => 1178, 'total_copies' => 3, 'available_copies' => 3, 'description' => 'An epic high-fantasy novel set in the fictional world of Middle-earth.'],
            ['title' => 'Sapiens: A Brief History of Humankind', 'author' => 'Yuval Noah Harari', 'isbn' => '978-0-06-231609-7', 'genre' => 'Non-Fiction', 'publication_year' => 2011, 'publisher' => 'Harper', 'pages' => 443, 'total_copies' => 4, 'available_copies' => 4, 'description' => 'A survey of the history of humankind from the Stone Age to the 21st century.'],
            ['title' => 'Atomic Habits', 'author' => 'James Clear', 'isbn' => '978-0-7352-1129-2', 'genre' => 'Self-Help', 'publication_year' => 2018, 'publisher' => 'Avery', 'pages' => 320, 'total_copies' => 5, 'available_copies' => 5, 'description' => 'A practical guide to building good habits and breaking bad ones.'],
            ['title' => 'The Art of War', 'author' => 'Sun Tzu', 'isbn' => '978-1-59030-963-9', 'genre' => 'Philosophy', 'publication_year' => 500, 'publisher' => 'Shambhala', 'pages' => 273, 'total_copies' => 2, 'available_copies' => 2, 'description' => 'An ancient Chinese military treatise dating from the 5th century BC.'],
        ];

        foreach ($books as $bookData) {
            Book::firstOrCreate(
                ['isbn' => $bookData['isbn']],
                array_merge($bookData, ['language' => 'English', 'fine_per_day' => 0.50, 'borrow_days' => 14])
            );
        }

        $this->command->info('Sample books seeded!');
    }
}
