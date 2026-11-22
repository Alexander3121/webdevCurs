-- АвтоПрокат: схема БД и тестовые данные (MySQL / MariaDB)
-- Импорт: phpMyAdmin -> Импорт, или  mariadb -u USER -p DBNAME < sql/autoprokat.sql
-- Тестовые пользователи: alex / demo12345, marina / demo12345

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `articles`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `cars`;
DROP TABLE IF EXISTS `users`;

-- ---------- Пользователи ----------
CREATE TABLE `users` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nickname`      VARCHAR(40)  NOT NULL,
    `email`         VARCHAR(120) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_nickname` (`nickname`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Автомобили ----------
CREATE TABLE `cars` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `brand`         VARCHAR(50)  NOT NULL,
    `model`         VARCHAR(50)  NOT NULL,
    `class`         VARCHAR(20)  NOT NULL,          -- economy | comfort | business | suv | minivan
    `year`          SMALLINT     NOT NULL,
    `transmission`  VARCHAR(10)  NOT NULL,          -- auto | manual
    `fuel`          VARCHAR(20)  NOT NULL,
    `seats`         TINYINT      NOT NULL DEFAULT 5,
    `power_hp`      SMALLINT     NOT NULL,
    `price_per_day` INT UNSIGNED NOT NULL,
    `deposit`       INT UNSIGNED NOT NULL,
    `color`         CHAR(7)      NOT NULL DEFAULT '#888888',
    `description`   TEXT         NOT NULL,
    `is_available`  TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Статьи ----------
CREATE TABLE `articles` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `author_id`  INT UNSIGNED NOT NULL,
    `title`      VARCHAR(200) NOT NULL,
    `excerpt`    VARCHAR(400) NOT NULL,
    `body`       TEXT         NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME     NULL,
    PRIMARY KEY (`id`),
    KEY `ix_articles_author` (`author_id`),
    CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Бронирования ----------
CREATE TABLE `bookings` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NOT NULL,
    `car_id`     INT UNSIGNED NOT NULL,
    `date_from`  DATE         NOT NULL,
    `date_to`    DATE         NOT NULL,
    `days`       SMALLINT     NOT NULL,
    `options`    VARCHAR(255) NOT NULL DEFAULT '',  -- коды опций через запятую
    `driver_age` TINYINT      NOT NULL,
    `experience` TINYINT      NOT NULL,
    `total`      INT UNSIGNED NOT NULL,
    `status`     VARCHAR(20)  NOT NULL DEFAULT 'new', -- new | confirmed | cancelled
    `comment`    VARCHAR(500) NOT NULL DEFAULT '',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `ix_bookings_user` (`user_id`),
    CONSTRAINT `fk_bookings_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bookings_car`  FOREIGN KEY (`car_id`)  REFERENCES `cars` (`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Обращения (обратная связь) ----------
CREATE TABLE `messages` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(80)  NOT NULL,
    `email`      VARCHAR(120) NOT NULL,
    `type`       VARCHAR(20)  NOT NULL,             -- question | complaint | suggestion | thanks
    `text`       TEXT         NOT NULL,
    `reply_via`  VARCHAR(40)  NOT NULL DEFAULT '',  -- sms,email
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =================== Тестовые данные ===================

INSERT INTO `users` (`id`, `nickname`, `email`, `password_hash`) VALUES
(1, 'alex',   'alex@autoprokat.test',   '$2y$10$sves90OPAq6F70HOwy6Ux.gSjBEjr/8kfu9LW14qlWwIy62KOicAS'),
(2, 'marina', 'marina@autoprokat.test', '$2y$10$XNP3047G1SKwvsGue4CRpuMdWWGl2KcY1QH6LKNAWh72MCXmoQzM6');

INSERT INTO `cars` (`brand`, `model`, `class`, `year`, `transmission`, `fuel`, `seats`, `power_hp`, `price_per_day`, `deposit`, `color`, `description`, `is_available`) VALUES
('Kia',        'Rio',           'economy',  2022, 'auto',   'бензин', 5, 123, 2300,  10000, '#c0392b', 'Экономичный городской седан: расход около 6 л на 100 км, вместительный багажник, климат-контроль.', 1),
('Hyundai',    'Solaris',       'economy',  2023, 'manual', 'бензин', 5, 100, 2100,  10000, '#ecf0f1', 'Надёжный и простой автомобиль для поездок по городу и за город. Механическая коробка.', 1),
('Lada',       'Vesta',         'economy',  2023, 'manual', 'бензин', 5, 106, 1900,  8000,  '#2980b9', 'Самый доступный вариант в нашем парке. Подходит для начинающих водителей.', 1),
('Renault',    'Logan',         'economy',  2021, 'auto',   'бензин', 5, 102, 2000,  8000,  '#7f8c8d', 'Простой седан с автоматом и большим багажником — 510 литров.', 1),
('Skoda',      'Octavia',       'comfort',  2022, 'auto',   'бензин', 5, 150, 3400,  15000, '#27ae60', 'Лифтбэк с огромным багажником, мультимедиа с CarPlay, адаптивный круиз-контроль.', 1),
('Toyota',     'Camry',         'comfort',  2023, 'auto',   'бензин', 5, 200, 4800,  20000, '#1c1c1c', 'Тихий и комфортный седан бизнес-класса для деловых поездок и встреч в аэропорту.', 1),
('Volkswagen', 'Polo',          'comfort',  2022, 'auto',   'бензин', 5, 125, 2900,  12000, '#d35400', 'Компактный, но просторный внутри. Отличная шумоизоляция для своего класса.', 1),
('Mazda',      'CX-5',          'suv',      2022, 'auto',   'бензин', 5, 194, 5200,  25000, '#8e1c1c', 'Кроссовер с полным приводом: уверенно чувствует себя и на трассе, и на просёлке.', 1),
('Toyota',     'RAV4',          'suv',      2023, 'auto',   'гибрид', 5, 222, 5900,  25000, '#95a5a6', 'Гибридный полноприводный кроссовер — расход как у компактного седана.', 1),
('Haval',      'Jolion',        'suv',      2024, 'auto',   'бензин', 5, 150, 3900,  15000, '#16a085', 'Современный кроссовер с богатой комплектацией и камерой кругового обзора.', 1),
('Mercedes-Benz', 'E 200',      'business', 2022, 'auto',   'бензин', 5, 197, 9500,  50000, '#2c3e50', 'Представительский седан: кожаный салон, массаж сидений, панорамная крыша.', 1),
('BMW',        '520d',          'business', 2021, 'auto',   'дизель', 5, 190, 8900,  50000, '#34495e', 'Динамичный и экономичный бизнес-седан на дизельном топливе.', 0),
('Volkswagen', 'Multivan',      'minivan',  2021, 'auto',   'дизель', 7, 150, 7500,  30000, '#bdc3c7', 'Семь мест, раздвижные двери, столик в салоне — для большой семьи или компании.', 1),
('Hyundai',    'Staria',        'minivan',  2023, 'auto',   'дизель', 9, 177, 7900,  30000, '#f1c40f', 'Девятиместный минивэн с футуристичным дизайном и огромным салоном.', 1);

INSERT INTO `articles` (`author_id`, `title`, `excerpt`, `body`, `created_at`) VALUES
(1, 'Как выбрать автомобиль для аренды', 'Разбираемся, какой класс машины подойдёт для города, трассы и путешествия всей семьёй.',
 'Выбор машины начинается с задачи. Для поездок по городу хватит эконом-класса: такие автомобили компактны, легко паркуются и мало расходуют.\n\nЕсли впереди долгая трасса, присмотритесь к комфорт-классу — в нём тише салон и удобнее сиденья. Для путешествий за город и по грунтовым дорогам лучше взять кроссовер с полным приводом.\n\nКомпании из 6–9 человек удобнее всего на минивэне: одна машина вместо двух обходится дешевле.', '2026-09-02 10:00:00'),
(2, 'Что проверить при получении машины', 'Короткий чек-лист: на что смотреть, чтобы потом не было споров о царапинах.',
 'Обойдите автомобиль вместе с менеджером и сфотографируйте все повреждения кузова и стёкол — даже мелкие.\n\nПроверьте уровень топлива и пробег, они должны совпадать с актом передачи. Убедитесь, что в машине есть запасное колесо, домкрат, аптечка и знак аварийной остановки.\n\nСохраните фотографии до конца аренды — это лучшая защита при возврате.', '2026-09-15 12:30:00'),
(1, 'Страховка при аренде: ОСАГО, КАСКО и депозит', 'Чем отличается базовая страховка от полной и зачем нужен залог.',
 'Каждый автомобиль в прокате застрахован по ОСАГО — это покрывает ущерб другим участникам движения.\n\nПолная страховка (КАСКО) защищает саму арендованную машину. С ней в случае ДТП не придётся платить за ремонт из своего кармана.\n\nДепозит — это сумма, которая блокируется на карте на время аренды. Если машина возвращена без повреждений и с полным баком, депозит полностью возвращается.', '2026-10-01 09:15:00'),
(2, 'Аренда для молодых водителей', 'Почему для водителей младше 23 лет или со стажем меньше двух лет действует надбавка.',
 'Статистика показывает, что начинающие водители чаще попадают в аварии. Поэтому для водителей младше 23 лет или со стажем меньше двух лет стоимость аренды увеличивается на 20%.\n\nСовет: если в поездке есть опытный водитель, оформите его основным, а себя — дополнительным.', '2026-10-12 16:40:00'),
(1, 'Скидки за длительную аренду', 'Чем дольше аренда, тем дешевле сутки — рассказываем, как работают скидки.',
 'При аренде от 7 суток действует скидка 10%, от 14 суток — 15%, а при аренде на месяц и дольше — 25%.\n\nСкидка применяется к стоимости самой машины, а дополнительные опции считаются отдельно. Посчитать точную сумму можно в нашем калькуляторе.', '2026-10-25 11:00:00'),
(2, 'Путешествие на машине по Золотому кольцу', 'Маршрут на 5 дней: Сергиев Посад, Переславль, Ростов, Ярославль, Суздаль.',
 'Золотое кольцо удобно проезжать на машине: расстояния между городами небольшие, а дороги хорошие.\n\nДень 1 — Сергиев Посад, день 2 — Переславль-Залесский и Ростов Великий, день 3 — Ярославль, день 4 — Кострома, день 5 — Суздаль и Владимир.\n\nДля такой поездки подойдёт комфорт-класс или кроссовер.', '2026-11-03 14:20:00'),
(1, 'Зимняя аренда: что нужно знать', 'Шипованная резина, незамерзайка и прогрев — всё уже включено.',
 'С ноября по март все наши автомобили ездят на зимней шипованной резине. Омывающая жидкость заливается морозостойкая.\n\nВ сильные морозы советуем прогревать двигатель 3–5 минут и не оставлять машину с почти пустым баком.', '2026-11-18 08:45:00');

INSERT INTO `bookings` (`user_id`, `car_id`, `date_from`, `date_to`, `days`, `options`, `driver_age`, `experience`, `total`, `status`, `comment`) VALUES
(1, 5, '2026-12-10', '2026-12-13', 3, 'gps', 27, 6, 10800, 'confirmed', ''),
(2, 8, '2026-12-20', '2026-12-27', 7, 'child_seat,insurance', 31, 10, 39774, 'new', 'Нужно детское кресло на 2–4 года');

INSERT INTO `messages` (`name`, `email`, `type`, `text`, `reply_via`) VALUES
('Ольга', 'olga@example.com', 'thanks', 'Спасибо за быструю выдачу машины!', 'email');
