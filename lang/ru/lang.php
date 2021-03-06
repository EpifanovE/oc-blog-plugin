<?php return [
    'plugin' => [
        'name' => 'Блог',
        'description' => '',
    ],
    'question' => 'Вопрос',
    'answer' => 'Ответ',
    'created_at' => 'Создано',
    'updated_at' => 'Обновлено',
    'faq' => 'Вопрос / Ответ',
    'corporate-site' => 'Корпоративный сайт',
    'general-params' => 'Общие параметры',
    'title' => 'Заголовок',
    'subtitle' => 'Подзаголовок',
    'content' => 'Содержимое',
    'advanced' => 'Дополнительно',
    'seo' => 'Поисковая оптимизация',
    'slug' => 'Псевдоним',
    'image' => 'Изображение',
    'preview' => 'Предпросмотр',
    'posts' => 'Записи',
    'blog' => 'Блог',
    'categories' => 'Категории',
    'tags' => 'Теги',
    'post-categories' => 'Категории записей',
    'post-tags' => 'Теги записей блога',
    'user' => 'Пользователь',
    'post' => 'Запись блога',
    'comments' => 'Комментарии',
    'is_moderated' => 'Проверен',
    'status' => 'Статус',
    'statuses' => [
        'published' => 'Опубликовано',
        'draft' => 'Черновик',
        'disabled' => 'Отключено',
    ],
    'creating_post' => 'Создание записи',
    'updating_post' => 'Обновление записи',
    'preview_post' => 'Просмотр записи',
    'creating' => 'Создание',
    'updating' => 'Обновление',
    'sorting' => 'Сортировка',
    'seo_title' => 'Заголовок страницы',
    'in_title_tag' => 'Тег <title>',
    'seo_description' => 'Описание',
    'in_tag_description' => 'Тег <meta name="description">',
    'seo_keywords' => 'Ключевые слова',
    'in_tag_keywords' => 'Тег <meta name="keywords">',
    'select-user' => 'Выберите пользователя',
    'select-admin' => 'Выберите администратора',
    'select-post' => 'Выберите запись',
    'admin' => 'Администратор',
    'author' => 'Автор',
    'record_has_been_saved' => 'Запись сохранена',
    'record_has_been_deleted' => 'Запись удалена',
    'category_page' => 'Страница категории',
    'tag_page' => 'Страница тега',
    'post_slug' => 'Псевдоним поста (slug)',
    'settings' => [
        'settings' => 'Настройки',
        'category_title' => 'Список категорий блога',
        'category_description' => 'Отображает список категорий на странице.',
        'category_slug' => 'Параметр URL',
        'category_slug_description' => 'Параметр маршрута, используемый для поиска в текущей категории по URL. Это свойство используется по умолчанию компонентом Фрагменты для маркировки активной категории.',
        'category_display_empty' => 'Пустые категории',
        'category_display_empty_description' => 'Отображать категории, которые не имеют записей.',
        'category_page' => 'Страница категорий',
        'category_page_description' => 'Название страницы категорий. Это свойство используется по умолчанию компонентом Фрагменты.',
        'post_title' => 'Запись блога',
        'post_description' => 'Отображение записи блога',
        'post_slug' => 'Параметр URL',
        'post_slug_description' => 'Параметр маршрута, необходимый для выбора конкретной записи.',
        'post_category' => 'Страница категорий',
        'post_category_description' => 'Название страницы категорий. Это свойство используется по умолчанию компонентом Фрагменты.',
        'posts_title' => 'Список записей блога',
        'posts_description' => 'Отображает список последних записей блога на странице.',
        'posts_pagination' => 'Параметр постраничной навигации',
        'posts_pagination_description' => 'Параметр, необходимый для постраничной навигации.',
        'posts_filter' => 'Фильтр категорий',
        'posts_filter_description' => 'Введите URL категории или параметр URL-адреса для фильтрации записей. Оставьте пустым, чтобы посмотреть все записи.',
        'posts_per_page' => 'Записей на странице',
        'posts_per_page_validation' => 'Недопустимый Формат. Ожидаемый тип данных - действительное число.',
        'posts_no_posts' => 'Отсутствие записей',
        'posts_no_posts_description' => 'Сообщение, отображаемое в блоге, если отсутствуют записи. Это свойство используется по умолчанию компонентом Фрагменты.',
        'posts_no_posts_default' => 'Записей не найдено',
        'posts_order' => 'Сортировка',
        'posts_order_description' => 'Атрибут, по которому будут сортироваться записи.',
        'posts_category' => 'Страница категорий',
        'posts_category_description' => 'Название категории на странице записи "размещена в категории". Это свойство используется по умолчанию компонентом Фрагменты.',
        'posts_post' => 'Страница записи',
        'posts_post_description' => 'Название страницы для ссылки "подробнее". Это свойство используется по умолчанию компонентом Фрагменты.',
        'posts_except_post' => 'Кроме записи',
        'posts_except_post_description' => 'Введите ID/URL или переменную с ID/URL записи, которую вы хотите исключить',
        'posts_except_categories' => 'Кроме категорий',
        'posts_except_categories_description' => 'Введите разделенный запятыми список URL категорий или переменную со списком категорий, которые вы хотите исключить',
        'rssfeed_blog' => 'Страница блога',
        'rssfeed_blog_description' => 'Имя основного файла страницы блога для генерации ссылок. Это свойство используется по умолчанию компонентом Фрагменты.',
        'rssfeed_title' => 'RSS Feed',
        'rssfeed_description' => 'Создает RSS-канал, содержащий записи из блога.',
        'group_links' => 'Ссылки',
        'group_exceptions' => 'Исключения'
    ],
];