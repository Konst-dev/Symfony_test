Запуск приложения:
1. Перейти в рабочую директорию
2. Выполнить команду git clone https://github.com/Konst-dev/Symfony_test
3. Перейти в папку Symfony_test
4. Если нет композера, установить его
5. Выполнить команду composer install
6. Cоздать базу данных MySQL
7. Импортировать туда таблицы из файла Symfony.sql в корневой папке проекта
8. Настроить доступ к БД в .env в корневой папке проекта
9. выполнить команду symfony console fill-database для заполнения БД (эта команда была создана по техническому заданию)
10. выполнить команду symfony console delete-authors-without-books для удаления авторов без книг (эта команда была создана по техническому заданию)
11. Настроить сервер для запуска веб приложения. Точка входа <путь к папке проекта>/public/index.php
12. Открыть в браузере веб приложение. На главной странице должны отобразиться все книги.
13. Пути для запросов HTTP API:
    /books/getallbooks - возвращает JSON со всеми книгами. GET;
    /books/createnewauthor - записывает в БД нового автора. POST. Вход: массив new_author с ключами last_name и first_name
    /books/addnewbook - сохраняет новую книгу.POST. Вход: массив new_book с ключами title, year, author(массив), publisher
    /books/editpublisher - сохрааняет новые данные об издателе. POST. Вход: массив edit_publisher с ключами id, name, address
    /books/deletebook - удаляет книгу.POST Вход: массив delete_book c ключом id
    /books/deleteauthor - удаляет Автора. POST Вход: массив delete_author c ключом id
    /books/deletepublisher - удаляет Издателя (Каскадное удаление). POST. Вход: массив delete_publisher c ключом id
14. Для проверки вышеописанных методов созданы страницы с формами:
    /books/newauthorform - добавление автора
    /books/newbookform - добавление книги
    /books/editpublisherform/{id} - редактирование издателя
    /books/deletebookform - удаление книги
    /books/deleteauthorform - удаление автора
    /books/deletepublisherform - удаление издателя

