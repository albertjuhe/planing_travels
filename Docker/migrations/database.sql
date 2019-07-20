CREATE DATABASE IF NOT EXISTS travelGuuid;

USE travelGuuid;

create table if not exists event
(
    id          int auto_increment
        primary key,
    body        longtext     not null,
    type_name   varchar(255) not null,
    occurred_on datetime     not null
)
    collate = utf8mb4_unicode_ci;

create table if not exists mark
(
    id          varchar(150)   not null
        primary key,
    title       varchar(255)   not null,
    created_at  datetime       not null,
    updated_at  datetime       not null,
    description longtext       null,
    lat         decimal(14, 8) not null,
    lng         decimal(14, 8) not null,
    lat0        decimal(14, 8) null,
    lng0        decimal(14, 8) null,
    lat1        decimal(14, 8) null,
    lng1        decimal(14, 8) null
)
    collate = utf8mb4_unicode_ci;

create table if not exists typelocation
(
    id          int auto_increment
        primary key,
    title       varchar(255) not null,
    icon        varchar(255) null,
    created_at  datetime     not null,
    updated_at  datetime     not null,
    description longtext     null
)
    collate = utf8mb4_unicode_ci;

create table if not exists users
(
    id         int auto_increment
        primary key,
    username   varchar(25)  not null,
    password   varchar(64)  not null,
    email      varchar(255) not null,
    is_active  tinyint(1)   not null,
    created_at datetime     not null,
    updated_at datetime     not null,
    last_login datetime     null,
    locale     varchar(100) not null,
    first_name varchar(100) not null,
    last_name  varchar(100) not null,
    constraint UNIQ_1483A5E9E7927C74
        unique (email),
    constraint UNIQ_1483A5E9F85E0677
        unique (username)
)
    collate = utf8mb4_unicode_ci;

create table if not exists travel
(
    id          char(36)       not null comment '(DC2Type:TravelId)'
        primary key,
    user_id     int            null,
    created_at  datetime       not null,
    updated_at  datetime       not null,
    title       varchar(255)   not null,
    slug        varchar(500)   not null,
    photo       varchar(255)   null,
    start_at    datetime       not null,
    end_at      datetime       not null,
    description longtext       null,
    stars       int            null,
    watch       int            null,
    status      int            null,
    lat         decimal(14, 8) not null,
    lng         decimal(14, 8) not null,
    lat0        decimal(14, 8) null,
    lng0        decimal(14, 8) null,
    lat1        decimal(14, 8) null,
    lng1        decimal(14, 8) null,
    constraint FK_2D0B6BCEA76ED395
        foreign key (user_id) references users (id)
)
    collate = utf8mb4_unicode_ci;

create table if not exists gpx
(
    id          int auto_increment
        primary key,
    travel_id   char(36)     null comment '(DC2Type:TravelId)',
    title       varchar(255) not null,
    description varchar(255) not null,
    filename    varchar(255) not null,
    color       varchar(255) not null,
    created_at  datetime     not null,
    updated_at  datetime     not null,
    constraint FK_C338844FECAB15B3
        foreign key (travel_id) references travel (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_C338844FECAB15B3
    on gpx (travel_id);

create table if not exists location
(
    id              char(36)     not null comment '(DC2Type:LocationId)'
        primary key,
    mark_id         varchar(150) null,
    travel_id       char(36)     null comment '(DC2Type:TravelId)',
    created_at      datetime     not null,
    updated_at      datetime     not null,
    title           varchar(255) not null,
    url             varchar(255) null,
    slug            varchar(500) not null,
    description     longtext     null,
    stars           int          null,
    typeLocation_id int          null,
    constraint FK_5E9E89CB4290F12B
        foreign key (mark_id) references mark (id),
    constraint FK_5E9E89CBECAB15B3
        foreign key (travel_id) references travel (id),
    constraint FK_5E9E89CBFE998804
        foreign key (typeLocation_id) references typelocation (id)
)
    collate = utf8mb4_unicode_ci;

create table if not exists images
(
    id          int auto_increment
        primary key,
    location_id char(36)     null comment '(DC2Type:LocationId)',
    original    varchar(255) not null,
    filename    varchar(255) not null,
    created_at  datetime     not null,
    updated_at  datetime     not null,
    constraint FK_E01FBE6A64D218E
        foreign key (location_id) references location (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_E01FBE6A64D218E
    on images (location_id);

create index IDX_5E9E89CB4290F12B
    on location (mark_id);

create index IDX_5E9E89CBECAB15B3
    on location (travel_id);

create index IDX_5E9E89CBFE998804
    on location (typeLocation_id);

create table if not exists note
(
    id          int auto_increment
        primary key,
    location_id char(36)     null comment '(DC2Type:LocationId)',
    title       varchar(255) not null,
    description varchar(255) not null,
    constraint FK_CFBDFA1464D218E
        foreign key (location_id) references location (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_CFBDFA1464D218E
    on note (location_id);

create index IDX_2D0B6BCEA76ED395
    on travel (user_id);

create table if not exists travels_shared
(
    travel_id int      not null,
    user_id   char(36) not null comment '(DC2Type:TravelId)',
    primary key (travel_id, user_id),
    constraint FK_8525D330A76ED395
        foreign key (user_id) references travel (id),
    constraint FK_8525D330ECAB15B3
        foreign key (travel_id) references users (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_8525D330A76ED395
    on travels_shared (user_id);

create index IDX_8525D330ECAB15B3
    on travels_shared (travel_id);


INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (1, 'House', 'fa fa-bed', '2015-08-07 17:57:18', '2015-08-07 17:57:18', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (2, 'Airport', 'fa fa-plane', '2015-08-07 17:57:48', '2015-08-07 17:57:48', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (3, 'Monument', 'fa fa-camera', '2015-08-07 17:58:08', '2015-08-07 17:58:08', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (4, 'City', 'fa fa-building', '2015-08-07 17:58:21', '2015-08-07 17:58:21', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (5, 'Lunch', 'fa fa-cutlery', '2015-08-07 17:59:29', '2015-08-07 17:59:29', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (6, 'Bicycle', 'fa fa-bicycle', '2015-08-07 17:59:46', '2015-08-07 17:59:46', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (7, 'Bus', 'fa fa-bus', '2015-08-07 18:00:07', '2015-08-07 18:00:07', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (8, 'Automobile', 'fa fa-automobile', '2015-08-07 18:00:27', '2015-08-07 18:00:27', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (9, 'Train', 'fa fa-train', '2015-08-07 18:00:47', '2015-08-07 18:00:47', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (10, 'Ship', 'fa fa-ship', '2015-08-07 18:00:59', '2015-08-07 18:00:59', null);
INSERT INTO travelGuuid.typelocation (id, title, icon, created_at, updated_at, description) VALUES (11, 'Coffee', 'fa fa-coffee', '2015-08-07 18:01:14', '2015-08-07 18:01:14', null);

INSERT INTO travelGuuid.users (id, username, password, email, is_active, created_at, updated_at, last_login, locale, first_name, last_name) VALUES (4, 'dummy', '$2y$13$PTT7vbovhdfvZhoMeYou8./P.8eVyrgwXoyy7dJvjN02Cowtc1vJW', 'dummy@dummy.com', 1, '2019-06-16 17:06:22', '2019-06-16 17:06:22', null, 'en', 'dummy', 'dummy');

INSERT INTO travelGuuid.travel (id, user_id, created_at, updated_at, title, slug, photo, start_at, end_at, description, stars, watch, status, lat, lng, lat0, lng0, lat1, lng1) VALUES ('9c7299d3-665b-4469-ba47-9020c38e91d7', 4, '2019-06-16 17:15:39', '2019-06-16 17:15:39', 'Toscana, Italia', 'toscana-italia-1', null, '2015-09-03 00:00:00', '2015-09-12 00:00:00', 'Toscana Profunda', 0, 0, 20, 43.77105130, 11.24862080, 45.32931298, 14.73692414, 41.33593303, 7.32115266);

