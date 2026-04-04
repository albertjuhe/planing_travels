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

create table if not exists migration_versions
(
    version     varchar(14) not null
        primary key,
    executed_at datetime    not null comment '(DC2Type:datetime_immutable)'
)
    collate = utf8mb4_unicode_ci;

create table if not exists password_reset_tokens
(
    id         int auto_increment
        primary key,
    user_id    int          not null,
    token_hash varchar(64)  not null,
    created_at datetime     not null,
    expires_at datetime     not null,
    used_at    datetime     null,
    constraint UNIQ_E151F57A4FA70B0
        unique (token_hash),
    constraint FK_E151F57A76ED395
        foreign key (user_id) references users (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create index IDX_E151F57A76ED395
    on password_reset_tokens (user_id);

create table if not exists travels_shared
(
    travel_id char(36) not null comment '(DC2Type:TravelId)',
    user_id   int      not null,
    primary key (travel_id, user_id),
    constraint FK_8525D330ECAB15B3
        foreign key (travel_id) references travel (id),
    constraint FK_8525D330A76ED395
        foreign key (user_id) references users (id)
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

INSERT INTO travelGuuid.travel (id, user_id, created_at, updated_at, title, slug, photo, start_at, end_at, description, stars, watch, status, lat, lng, lat0, lng0, lat1, lng1) VALUES ('9c7299d3-665b-4469-ba47-9020c38e91d7', 4, '2019-06-16 17:15:39', '2019-06-16 17:15:39', 'Toscana, Italia', 'toscana-italia-1', 'traditional-toscana-italy-landscape-hills-fields-sky_1028938-185353-3021269073-69cf6b57b5c42.jpg', '2015-09-03 00:00:00', '2015-09-12 00:00:00', 'Toscana Profunda', 0, 0, 20, 43.77105130, 11.24862080, 45.32931298, 14.73692414, 41.33593303, 7.32115266);
INSERT INTO travelGuuid.travel (id, user_id, created_at, updated_at, title, slug, photo, start_at, end_at, description, stars, watch, status, lat, lng, lat0, lng0, lat1, lng1) VALUES ('b10b8ae9-451c-4195-97c3-8c99dc63c123',	4,	'2023-06-16 06:37:27',	'2023-06-16 06:37:27',	'UTMB Montblanc',	'utmb-montblanc',	'snowy-mountains-chamonix-mont-blanc-hautesavoie-alps-france_652249-3513-2563027050-69cf6b69db17b.jpg',	'2023-08-27 00:00:00',	'2023-09-06 00:00:00',	'Vacances als Alps',	0,	0,	10,	45.92467050,	6.87275060,	0.00000000,	0.00000000,	0.00000000,	0.00000000);
INSERT INTO travelGuuid.travel (id, user_id, created_at, updated_at, title, slug, photo, start_at, end_at, description, stars, watch, status, lat, lng, lat0, lng0, lat1, lng1) VALUES ('8370ec26-591f-40ab-8746-10fff8d27991',	4,	'2024-08-30 09:33:11',	'2024-08-30 09:33:11',	'Viatge a Creta',	'viatge-a-creta',	'Islands-Near-Crete-Your-Ultimate-Guide-to-Exploring-Hidden-Gems-680x290-197739012-69cf6bc58fb06.jpg',	'2024-08-07 00:00:00',	'2024-08-14 00:00:00',	'Viatge de Noces a Creta',	0,	0,	10,	35.30849520,	24.46334232,	0.00000000,	0.00000000,	0.00000000,	0.00000000);
INSERT INTO travelGuuid.travel (`id`, `user_id`, `created_at`, `updated_at`, `title`, `slug`, `photo`, `start_at`, `end_at`, `description`, `stars`, `watch`, `status`, `lat`, `lng`, `lat0`, `lng0`, `lat1`, `lng1`) VALUES ('3e778d33-24d9-49e0-93d3-5a8c630d5520',	4,	'2026-04-03 07:45:09',	'2026-04-03 07:45:09',	'Highlands Scotland',	'highlands-scotland',	'eilean-donan-castle-69d0edbada27b.jpeg',	'2003-09-11 00:00:00',	'2003-09-20 00:00:00',	'Primeres vacances llargues a escocia',	0,	0,	10,	56.78611120,	-4.11405180,	0.00000000,	0.00000000,	0.00000000,	0.00000000);

INSERT INTO travelGuuid.mark (id, title, created_at, updated_at, description, lat, lng, lat0, lng0, lat1, lng1) VALUES
('ChIJ0Qkj_kMaKhMRIdNhZD2E4i4',	'Volterra',	'2020-04-05 16:52:54',	'2020-04-05 16:52:54',	'{\"placeAddress\":\"56048 Volterra, Pisa, Italia\",\"IdType\":\"4\",\"link\":\"https://www.turismotoscana.es/ciudades-de-toscana/montepulciano\",\"comment\":\"Volterra és una ciutat de Toscana a Itàlia, a la província de Pisa, amb uns 15.000 habitants. Està situada entre les valls de l\'Era i del Cecina. Té jaciments de sal i indústria d\'alabastre i és seu episcopal.\",\"latitude\":43.399395,\"longitude\":10.8660333,\"place_id\":\"ChIJ0Qkj_kMaKhMRIdNhZD2E4i4\",\"address\":\"Volterra\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.39939500,	10.86603330,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJ0YvARycuKhMRzWQ7D_I9S4Q',	'Monteriggioni',	'2020-04-05 16:56:53',	'2020-04-05 16:56:53',	'{\"placeAddress\":\"53035 Monteriggioni, Siena, Italia\",\"IdType\":\"4\",\"link\":\"https://www.turismotoscana.es/ciudades-de-toscana/montepulcianohttps://es.wikipedia.org/wiki/Monteriggioni\",\"comment\":\"Monteriggioni es un municipio de 8.701 habitantes de la provincia de Siena en la región italiana de Toscana. Está rodeado por los municipios de Casole d\'Elsa, Castellina in Chianti, Castelnuovo Berardenga, Colle di Val d\'Elsa, Poggibonsi, Siena y Sovicille.\\n\\nHoy, la ciudad de Monteriggioni es el centro principal en el moderno municipio de Monteriggioni que abarca 19,49 kilómetros cuadrados en la zona que rodea la ciudad. Las distancias a otras ciudades principales son: Siena - 15 km; Volterra - 39 km; Florencia - 50 km; Pisa - 157 km; Lucca - 123 km; Arezzo - 121 km; Roma - 250 km.\",\"latitude\":43.3901353,\"longitude\":11.2233863,\"place_id\":\"ChIJ0YvARycuKhMRzWQ7D_I9S4Q\",\"address\":\"Monteriggioni\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.39013530,	11.22338630,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJ14KngXj5KxMRTnNw3KF_zyc',	'Cortona, bajo el sol de la toscana',	'2020-04-05 16:53:58',	'2020-04-05 16:53:58',	'{\"placeAddress\":\"52044 Cortona, Arezzo, Italia\",\"IdType\":\"4\",\"link\":\"https://www.turismotoscana.es/ciudades-de-toscana/montepulciano\",\"comment\":\"Cortona es un monumento histórico y arquitectónico ya que se trata de población de origen etrusco más antigua en Toscana. Sus grandiosas murallas debieron infligir respeto a los que miraban la ciudad enrocada en la colina de San Egidio, en la parte oriental del ValdiChiana, y tanto fue así que ni los hostigamientos de las ciudades de Arezzo y Siena consiguieron subyugarla en la Edad Media. Hoy en día es una pequeña ciudad de 23.000 habitantes, pero su casco antiguo está preservado y protegido.\",\"latitude\":43.27506340000001,\"longitude\":11.98512,\"place_id\":\"ChIJ14KngXj5KxMRTnNw3KF_zyc\",\"address\":\"Cortona, bajo el sol de la toscana\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.27506340,	11.98512000,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJ7wjtUJqD1RIR4dlNZZImKnk',	'Lucca',	'2020-04-05 17:02:37',	'2020-04-05 17:02:37',	'{\"placeAddress\":\"55100 Lucca, Italia\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":43.8429197,\"longitude\":10.5026977,\"place_id\":\"ChIJ7wjtUJqD1RIR4dlNZZImKnk\",\"address\":\"Lucca\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.84291970,	10.50269770,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJb2tEMTg8KhMRkUT1UlT1Tkw',	'San Gimingnano en la Toscana  ',	'2020-04-05 16:48:15',	'2020-04-05 16:48:15',	'{\"placeAddress\":\"53037 San Gimignano, Siena, Italia\",\"IdType\":\"4\",\"link\":\"https://guias-viajar.com/italia/toscana/visita-san-gimignano/\",\"comment\":\"El pueblo de las torres medievales en Toscana\",\"latitude\":43.4676324,\"longitude\":11.0434909,\"place_id\":\"ChIJb2tEMTg8KhMRkUT1UlT1Tkw\",\"address\":\"San Gimingnano en la Toscana  \",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.46763240,	11.04349090,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJB7PqqwRcKRMRkj8tZn5mvc8',	'Montepulciano (Mons Politianus) ',	'2020-04-05 16:50:48',	'2020-04-05 16:50:48',	'{\"placeAddress\":\"53045 Montepulciano, Siena, Italia\",\"IdType\":\"4\",\"link\":\"https://www.turismotoscana.es/ciudades-de-toscana/montepulciano\",\"comment\":\"Encaramada en una cima de colinas, Montepulciano (Mons Politianus) se yergue al sur de la Toscana, no muy lejos de Siena, como si sus palacios renacentistas buscaran ensalzar su belleza aún más. Tierra del afamado Vino Nobile, los viñedos que rodean la ciudad, nutren de uva a las bodegas que consiguen vinos de una calidad reconocida por todo el mundo.\",\"latitude\":43.0986938,\"longitude\":11.7872467,\"place_id\":\"ChIJB7PqqwRcKRMRkj8tZn5mvc8\",\"address\":\"Montepulciano (Mons Politianus) \",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.09869380,	11.78724670,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJE1O_NL8sKhMR40Mj8RISc10',	'siena',	'2020-04-05 16:46:40',	'2020-04-05 16:46:40',	'{\"placeAddress\":\"53100 Siena, Italia\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":43.31880899999999,\"longitude\":11.3307574,\"place_id\":\"ChIJE1O_NL8sKhMR40Mj8RISc10\",\"address\":\"siena\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.31880900,	11.33075740,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJFaj983LtKxMRCNw0h7HxRvg',	'Arezzo',	'2020-04-05 17:02:17',	'2020-04-05 17:02:17',	'{\"placeAddress\":\"52100 Arezzo, Italia\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":43.46328390000001,\"longitude\":11.8796336,\"place_id\":\"ChIJFaj983LtKxMRCNw0h7HxRvg\",\"address\":\"Arezzo\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.46328390,	11.87963360,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJj1n28JqR1RIRyHiEp7UPuKo',	'Aeropuerto',	'2020-04-05 17:01:40',	'2020-04-05 17:01:40',	'{\"placeAddress\":\"Pisa, Italia\",\"IdType\":\"2\",\"link\":\"\",\"comment\":\"\",\"latitude\":43.7228386,\"longitude\":10.4016888,\"place_id\":\"ChIJj1n28JqR1RIRyHiEp7UPuKo\",\"address\":\"Aeropuerto\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.72283860,	10.40168880,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('ChIJVYndwwlnKRMR19o8AqCLwz4',	'Pienza',	'2020-04-05 16:52:03',	'2020-04-05 16:52:03',	'{\"placeAddress\":\"53026 Pienza, Siena, Italia\",\"IdType\":\"4\",\"link\":\"https://www.turismotoscana.es/ciudades-de-toscana/montepulciano\",\"comment\":\"Pienza és una ciutat i municipi de la província de Siena, a la vall de l\'Orcia a la Toscana, a Itàlia, entre les ciutats de Montepulciano i Montalcino, considerada la «pedra de toc de l\'urbanisme renaixentista». Viquipèdia\",\"latitude\":43.0774495,\"longitude\":11.6775951,\"place_id\":\"ChIJVYndwwlnKRMR19o8AqCLwz4\",\"address\":\"Pienza\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.07744950,	11.67759510,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('EitTdHIuIGRlaSBTZWx2b2xpbmksIE1vbnRlcmlnZ2lvbmkgU0ksIEl0YWx5Ii4qLAoUChIJeWc5SYwyKhMRNJgZgIFAsMASFAoSCakdtFRPLioTEfCl5eOQLAgE',	'MonteriggioniAllotjament Agriturismo Le Gallozzole',	'2020-04-05 17:00:47',	'2020-04-05 17:00:47',	'{\"placeAddress\":\"Str. dei Selvolini, Monteriggioni SI, Italia\",\"IdType\":\"1\",\"link\":\"http://www.agriturismolegallozzole.it/\",\"comment\":\"Turisme rural\",\"latitude\":43.4016385,\"longitude\":11.2965372,\"place_id\":\"EitTdHIuIGRlaSBTZWx2b2xpbmksIE1vbnRlcmlnZ2lvbmkgU0ksIEl0YWx5Ii4qLAoUChIJeWc5SYwyKhMRNJgZgIFAsMASFAoSCakdtFRPLioTEfCl5eOQLAgE\",\"address\":\"MonteriggioniAllotjament Agriturismo Le Gallozzole\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.40163850,	11.29653720,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('307604564',	'Aosta',	'2023-06-16 06:38:32',	'2023-06-16 06:38:32',	'{\"placeAddress\":\"Aosta, Aosta Valley, 11100, Italy\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"45.7370885\",\"longitude\":\"7.3196649\",\"place_id\":\"307604564\",\"address\":\"Aosta\",\"travel\":\"b10b8ae9-451c-4195-97c3-8c99dc63c123\",\"user\":\"4\",\"currentMark\":null}',	45.73708850,	7.31966490,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('368734843',	'Chania, Municipi de Khania, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, 731 36, Grècia',	'2024-08-30 09:40:04',	'2024-08-30 09:40:04',	'{\"placeAddress\":\"Chania, Municipi de Khania, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, 731 36, Grècia\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"35.5120831\",\"longitude\":\"24.0191544\",\"place_id\":\"368734843\",\"address\":\"Chania, Municipi de Khania, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, 731 36, Grècia\",\"travel\":\"8370ec26-591f-40ab-8746-10fff8d27991\",\"user\":\"4\",\"currentMark\":null}',	35.51208310,	24.01915440,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('47083486',	'Samaria Gorge entrance',	'2024-08-30 09:42:21',	'2024-08-30 09:42:21',	'{\"placeAddress\":\"Samaria Gorge entrance, Municipi de Plataniàs, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, Grècia\",\"IdType\":\"3\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"35.3078642\",\"longitude\":\"23.9183637\",\"place_id\":\"47083486\",\"address\":\"Samaria Gorge entrance\",\"travel\":\"8370ec26-591f-40ab-8746-10fff8d27991\",\"user\":\"4\",\"currentMark\":null}',	35.30786420,	23.91836370,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('47181774',	'Hersonissos',	'2024-08-30 09:38:26',	'2024-08-30 09:38:26',	'{\"placeAddress\":\"Downtown Hersonissos, 177, Ελευθερίου Βενιζέλου, Limenas Chersonisou, Chersonisos Municipal Unit, Municipi de Khersónissos, Unitat perifèrica Iràklio, Perifèria de Creta, Creta, 700 14, Grècia\",\"IdType\":\"1\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"35.312571\",\"longitude\":\"25.3971779\",\"place_id\":\"47181774\",\"address\":\"Downtown Hersonissos, 177, Ελευθερίου Βενιζέλου, Limenas Chersonisou, Chersonisos Municipal Unit, Municipi de Khersónissos, Unitat perifèrica Iràklio, Perifèria de Creta, Creta, 700 14, Grècia\",\"travel\":\"8370ec26-591f-40ab-8746-10fff8d27991\",\"user\":\"4\",\"currentMark\":null}',	35.31257100,	25.39717790,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('47182068',	'Spinalonga island',	'2024-08-30 09:43:00',	'2024-08-30 09:43:00',	'{\"placeAddress\":\"Spinalonga, Community of Elounta, Agios Nikolaos Municipal Unit, Municipality of Agios Nikolaos, Unitat perifèrica de Lassithi, Perifèria de Creta, Creta, 721 00, Grècia\",\"IdType\":\"3\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"35.2977829\",\"longitude\":\"25.738043952653506\",\"place_id\":\"47182068\",\"address\":\"Spinalonga island\",\"travel\":\"8370ec26-591f-40ab-8746-10fff8d27991\",\"user\":\"4\",\"currentMark\":null}',	35.29778290,	25.73804395,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('47255312',	'Rethymnon, Municipality of Rethymnon, Unitat perifèrica de Réthimno, Perifèria de Creta, Creta, 741 31, Grècia',	'2024-08-30 09:40:59',	'2024-08-30 09:40:59',	'{\"placeAddress\":\"Rethymnon, Municipality of Rethymnon, Unitat perifèrica de Réthimno, Perifèria de Creta, Creta, 741 31, Grècia\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"35.3676472\",\"longitude\":\"24.4736079\",\"place_id\":\"47255312\",\"address\":\"Rethymnon, Municipality of Rethymnon, Unitat perifèrica de Réthimno, Perifèria de Creta, Creta, 741 31, Grècia\",\"travel\":\"8370ec26-591f-40ab-8746-10fff8d27991\",\"user\":\"4\",\"currentMark\":null}',	35.36764720,	24.47360790,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('50620170',	'Elafonisi Beach',	'2024-08-30 09:44:42',	'2024-08-30 09:44:42',	'{\"placeAddress\":\"Elafonissi, Elafonisi Beach, Municipi de Kissamos, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, Grècia\",\"IdType\":\"3\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"35.26945465\",\"longitude\":\"23.531635154849305\",\"place_id\":\"50620170\",\"address\":\"Elafonisi Beach\",\"travel\":\"8370ec26-591f-40ab-8746-10fff8d27991\",\"user\":\"4\",\"currentMark\":null}',	35.26945465,	23.53163515,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('79500372',	'AIRPORT',	'2024-08-30 09:45:46',	'2024-08-30 09:45:46',	'{\"placeAddress\":\"AIRPORT, Ikarou Avenue, Community of Nea Alikarnassos, Nea Alikarnassos Municipal Unit, Municipality of Heraklion, Unitat perifèrica Iràklio, Perifèria de Creta, Creta, 716 01, Grècia\",\"IdType\":\"2\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"35.3360849\",\"longitude\":\"25.1732335\",\"place_id\":\"79500372\",\"address\":\"AIRPORT\",\"travel\":\"8370ec26-591f-40ab-8746-10fff8d27991\",\"user\":\"4\",\"currentMark\":null}',	35.33608490,	25.17323350,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('107120402',	'Les Houches Allotjament',	'2024-08-30 09:57:16',	'2024-08-30 09:57:16',	'{\"placeAddress\":\"Les Houches, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74310, França\",\"IdType\":\"1\",\"link\":\"\",\"comment\":\"Allotjament per la cursa\",\"latitude\":\"45.891508\",\"longitude\":\"6.799138\",\"place_id\":\"107120402\",\"address\":\"Les Houches Allotjament\",\"travel\":\"b10b8ae9-451c-4195-97c3-8c99dc63c123\",\"user\":\"4\",\"currentMark\":null}',	45.89150800,	6.79913800,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('107354879',	'Annecy',	'2024-08-30 09:56:27',	'2024-08-30 09:56:27',	'{\"placeAddress\":\"Annecy, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, França\",\"IdType\":\"1\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"45.8992348\",\"longitude\":\"6.1288847\",\"place_id\":\"107354879\",\"address\":\"Annecy\",\"travel\":\"b10b8ae9-451c-4195-97c3-8c99dc63c123\",\"user\":\"4\",\"currentMark\":null}',	45.89923480,	6.12888470,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('107544054',	'La mer de glaces',	'2024-08-30 10:02:46',	'2024-08-30 10:02:46',	'{\"placeAddress\":\"La mer de glaces, Avenue de l Aiguille du Midi, Les Favrands, Chamonix-Mont-Blanc, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74400, França\",\"IdType\":\"3\",\"link\":\"\",\"comment\":\"Visiat a la glaçera\",\"latitude\":\"45.9194757\",\"longitude\":\"6.8686409\",\"place_id\":\"107544054\",\"address\":\"La mer de glaces\",\"travel\":\"b10b8ae9-451c-4195-97c3-8c99dc63c123\",\"user\":\"4\",\"currentMark\":null}',	45.91947570,	6.86864090,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('107551267',	'Refuge du Lac Blanc',	'2024-08-30 10:01:55',	'2024-08-30 10:01:55',	'{\"placeAddress\":\"Refuge du Lac Blanc, Tour du Pays du Mont-Blanc, Chamonix-Mont-Blanc, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74400, França\",\"IdType\":\"3\",\"link\":\"\",\"comment\":\"Caminata\",\"latitude\":\"45.981721050000004\",\"longitude\":\"6.89187655395204\",\"place_id\":\"107551267\",\"address\":\"Refuge du Lac Blanc\",\"travel\":\"b10b8ae9-451c-4195-97c3-8c99dc63c123\",\"user\":\"4\",\"currentMark\":null}',	45.98172105,	6.89187655,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('75129326',	'Aiguille du Midi',	'2024-08-30 10:00:50',	'2024-08-30 10:00:50',	'{\"placeAddress\":\"Aiguille du Midi, Chamonix-Mont-Blanc, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74400, França\",\"IdType\":\"3\",\"link\":\"\",\"comment\":\"Visita al punt mes alt\",\"latitude\":\"45.8787035\",\"longitude\":\"6.8875506\",\"place_id\":\"75129326\",\"address\":\"Aiguille du Midi\",\"travel\":\"b10b8ae9-451c-4195-97c3-8c99dc63c123\",\"user\":\"4\",\"currentMark\":null}',	45.87870350,	6.88755060,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('75293901',	'Orsières sortida UTMB',	'2024-08-30 09:59:15',	'2024-08-30 09:59:15',	'{\"placeAddress\":\"Orsières, Entremont, Valais, Suïssa\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"Sortida\",\"latitude\":\"46.0300676\",\"longitude\":\"7.1453627\",\"place_id\":\"75293901\",\"address\":\"Orsières sortida UTMB\",\"travel\":\"b10b8ae9-451c-4195-97c3-8c99dc63c123\",\"user\":\"4\",\"currentMark\":null}',	46.03006760,	7.14536270,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('249452898',	'Oban, Argyll and Bute, Escòcia, PA34 4AT, Regne Unit',	'2026-04-04 10:43:40',	'2026-04-04 10:43:40',	'{\"placeAddress\":\"Oban, Argyll and Bute, Escòcia, PA34 4AT, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"56.4120166\",\"longitude\":\"-5.4723731\",\"place_id\":\"249452898\",\"address\":\"Oban, Argyll and Bute, Escòcia, PA34 4AT, Regne Unit\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	56.41201660,	-5.47237310,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('250746677',	'Elgin, Monestir defensa segona guerra mundial',	'2026-04-04 10:46:29',	'2026-04-04 10:46:29',	'{\"placeAddress\":\"Elgin, Moray, Escòcia, IV30 1EA, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"Poble molt bonic\",\"latitude\":\"57.6487891\",\"longitude\":\"-3.3148459\",\"place_id\":\"250746677\",\"address\":\"Elgin, Monestir defensa segona guerra mundial\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	57.64878910,	-3.31484590,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('253005868',	'Edimburg, Escòcia, Regne Unit',	'2026-04-04 10:44:38',	'2026-04-04 10:44:38',	'{\"placeAddress\":\"Edimburg, Escòcia, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"55.9533456\",\"longitude\":\"-3.1883749\",\"place_id\":\"253005868\",\"address\":\"Edimburg, Escòcia, Regne Unit\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	55.95334560,	-3.18837490,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('253103163',	'Portree, Consell de Highland, Escòcia, IV51 9EH, Regne Unit',	'2026-04-04 10:43:01',	'2026-04-04 10:43:01',	'{\"placeAddress\":\"Portree, Consell de Highland, Escòcia, IV51 9EH, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"57.4130532\",\"longitude\":\"-6.194446\",\"place_id\":\"253103163\",\"address\":\"Portree, Consell de Highland, Escòcia, IV51 9EH, Regne Unit\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	57.41305320,	-6.19444600,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('253794600',	'Illa de Mull, Argyll and Bute, Escòcia, Regne Unit',	'2026-04-04 10:45:05',	'2026-04-04 10:45:05',	'{\"placeAddress\":\"Illa de Mull, Argyll and Bute, Escòcia, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"56.4596987\",\"longitude\":\"-5.8620604\",\"place_id\":\"253794600\",\"address\":\"Illa de Mull, Argyll and Bute, Escòcia, Regne Unit\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	56.45969870,	-5.86206040,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('253832841',	'Inverness, Consell de Highland, Escòcia, IV1 1AN, Regne Unit',	'2026-04-04 10:45:36',	'2026-04-04 10:45:36',	'{\"placeAddress\":\"Inverness, Consell de Highland, Escòcia, IV1 1AN, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"57.4790124\",\"longitude\":\"-4.225739\",\"place_id\":\"253832841\",\"address\":\"Inverness, Consell de Highland, Escòcia, IV1 1AN, Regne Unit\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	57.47901240,	-4.22573900,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('254099028',	'Perth, Perth i Kinross, Escòcia, PH1 5TZ, Regne Unit',	'2026-04-04 10:45:56',	'2026-04-04 10:45:56',	'{\"placeAddress\":\"Perth, Perth i Kinross, Escòcia, PH1 5TZ, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"Poble molt bonic\",\"latitude\":\"56.3958757\",\"longitude\":\"-3.4303103\",\"place_id\":\"254099028\",\"address\":\"Perth, Perth i Kinross, Escòcia, PH1 5TZ, Regne Unit\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	56.39587570,	-3.43031030,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('254852643',	'Glasgow, Glasgow City, Escòcia, G2 1AL, Regne Unit',	'2026-04-04 10:44:16',	'2026-04-04 10:44:16',	'{\"placeAddress\":\"Glasgow, Glasgow City, Escòcia, G2 1AL, Regne Unit\",\"IdType\":\"4\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"55.861155\",\"longitude\":\"-4.2501687\",\"place_id\":\"254852643\",\"address\":\"Glasgow, Glasgow City, Escòcia, G2 1AL, Regne Unit\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	55.86115500,	-4.25016870,	0.00000000,	0.00000000,	0.00000000,	0.00000000),
('260852438',	'Eilean Donan Castle',	'2026-04-04 10:41:57',	'2026-04-04 10:41:57',	'{\"placeAddress\":\"Eilean Donan, Totaig, Dornie, Consell de Highland, Escòcia, Regne Unit\",\"IdType\":\"3\",\"link\":\"\",\"comment\":\"\",\"latitude\":\"57.2739885\",\"longitude\":\"-5.5150175\",\"place_id\":\"260852438\",\"address\":\"Eilean Donan Castle\",\"travel\":\"3e778d33-24d9-49e0-93d3-5a8c630d5520\",\"user\":\"4\",\"currentMark\":null}',	57.27398850,	-5.51501750,	0.00000000,	0.00000000,	0.00000000,	0.00000000);

INSERT INTO travelGuuid.location (id, mark_id, travel_id, created_at, updated_at, title, url, slug, description, stars, typeLocation_id) VALUES
('101ec877-61dc-4e2f-b76f-a16aa84eea16',	'ChIJb2tEMTg8KhMRkUT1UlT1Tkw',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 16:48:15',	'2020-04-05 16:48:15',	'53037 San Gimignano, Siena, Italia',	'https://guias-viajar.com/italia/toscana/visita-san-gimignano/',	'53037-san-gimignano-siena-italia',	'El pueblo de las torres medievales en Toscana',	NULL,	4),
('1b1bea85-cd82-4b57-9b7f-66616b45492c',	'ChIJE1O_NL8sKhMR40Mj8RISc10',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 16:46:39',	'2020-04-05 16:46:39',	'53100 Siena, Italia',	'',	'53100-siena-italia',	'',	NULL,	4),
('379f2d06-94b7-4a7f-bf9d-b76b39fc95d5',	'ChIJj1n28JqR1RIRyHiEp7UPuKo',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 17:01:40',	'2020-04-05 17:01:40',	'Pisa, Italia',	'',	'pisa-italia',	'',	NULL,	2),
('4b4bf738-0448-4c60-9fe5-f1ac358360d3',	'ChIJVYndwwlnKRMR19o8AqCLwz4',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 16:52:03',	'2020-04-05 16:52:03',	'53026 Pienza, Siena, Italia',	'https://www.turismotoscana.es/ciudades-de-toscana/montepulciano',	'53026-pienza-siena-italia',	'Pienza és una ciutat i municipi de la província de Siena, a la vall de l\'Orcia a la Toscana, a Itàlia, entre les ciutats de Montepulciano i Montalcino, considerada la «pedra de toc de l\'urbanisme renaixentista». Viquipèdia',	NULL,	4),
('513759bd-9236-4d64-afa5-199dbcb200b5',	'ChIJB7PqqwRcKRMRkj8tZn5mvc8',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 16:50:47',	'2020-04-05 16:50:47',	'53045 Montepulciano, Siena, Italia',	'https://www.turismotoscana.es/ciudades-de-toscana/montepulciano',	'53045-montepulciano-siena-italia',	'Encaramada en una cima de colinas, Montepulciano (Mons Politianus) se yergue al sur de la Toscana, no muy lejos de Siena, como si sus palacios renacentistas buscaran ensalzar su belleza aún más. Tierra del afamado Vino Nobile, los viñedos que rodean la ciudad, nutren de uva a las bodegas que consiguen vinos de una calidad reconocida por todo el mundo.',	NULL,	4),
('5448d717-1123-4c21-9c6b-70c3b2a664c1',	'ChIJ0Qkj_kMaKhMRIdNhZD2E4i4',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 16:52:54',	'2020-04-05 16:52:54',	'56048 Volterra, Pisa, Italia',	'https://www.turismotoscana.es/ciudades-de-toscana/montepulciano',	'56048-volterra-pisa-italia',	'Volterra és una ciutat de Toscana a Itàlia, a la província de Pisa, amb uns 15.000 habitants. Està situada entre les valls de l\'Era i del Cecina. Té jaciments de sal i indústria d\'alabastre i és seu episcopal.',	NULL,	4),
('86a188f8-1229-4908-9b08-5c246f014206',	'ChIJ14KngXj5KxMRTnNw3KF_zyc',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 16:53:58',	'2020-04-05 16:53:58',	'52044 Cortona, Arezzo, Italia',	'https://www.turismotoscana.es/ciudades-de-toscana/montepulciano',	'52044-cortona-arezzo-italia',	'Cortona es un monumento histórico y arquitectónico ya que se trata de población de origen etrusco más antigua en Toscana. Sus grandiosas murallas debieron infligir respeto a los que miraban la ciudad enrocada en la colina de San Egidio, en la parte oriental del ValdiChiana, y tanto fue así que ni los hostigamientos de las ciudades de Arezzo y Siena consiguieron subyugarla en la Edad Media. Hoy en día es una pequeña ciudad de 23.000 habitantes, pero su casco antiguo está preservado y protegido.',	NULL,	4),
('b3d4f35b-8421-4a8a-95a7-0705549cc82d',	'ChIJFaj983LtKxMRCNw0h7HxRvg',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 17:02:17',	'2020-04-05 17:02:17',	'52100 Arezzo, Italia',	'',	'52100-arezzo-italia',	'',	NULL,	4),
('c6e49939-3c7c-460d-a182-fd65c146106f',	'ChIJ0YvARycuKhMRzWQ7D_I9S4Q',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 16:56:53',	'2020-04-05 16:56:53',	'53035 Monteriggioni, Siena, Italia',	'https://www.turismotoscana.es/ciudades-de-toscana/montepulcianohttps://es.wikipedia.org/wiki/Monteriggioni',	'53035-monteriggioni-siena-italia',	'Monteriggioni es un municipio de 8.701 habitantes de la provincia de Siena en la región italiana de Toscana. Está rodeado por los municipios de Casole d\'Elsa, Castellina in Chianti, Castelnuovo Berardenga, Colle di Val d\'Elsa, Poggibonsi, Siena y Sovicille.\n\nHoy, la ciudad de Monteriggioni es el centro principal en el moderno municipio de Monteriggioni que abarca 19,49 kilómetros cuadrados en la zona que rodea la ciudad. Las distancias a otras ciudades principales son: Siena - 15 km; Volterra - 39 km; Florencia - 50 km; Pisa - 157 km; Lucca - 123 km; Arezzo - 121 km; Roma - 250 km.',	NULL,	4),
('e26c710b-6837-4907-b52c-7fc83a06378c',	'ChIJ7wjtUJqD1RIR4dlNZZImKnk',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 17:02:37',	'2020-04-05 17:02:37',	'55100 Lucca, Italia',	'',	'55100-lucca-italia',	'',	NULL,	4),
('f37671ca-e49f-41f8-9c20-d334452cd452',	'EitTdHIuIGRlaSBTZWx2b2xpbmksIE1vbnRlcmlnZ2lvbmkgU0ksIEl0YWx5Ii4qLAoUChIJeWc5SYwyKhMRNJgZgIFAsMASFAoSCakdtFRPLioTEfCl5eOQLAgE',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 17:00:47',	'2020-04-05 17:00:47',	'Str. dei Selvolini, Monteriggioni SI, Italia',	'http://www.agriturismolegallozzole.it/',	'str-dei-selvolini-monteriggioni-si-italia',	'Turisme rural',	NULL,	1),
('34c9be89-fdf8-4732-bdad-baf0f456489d',	'307604564',	'b10b8ae9-451c-4195-97c3-8c99dc63c123',	'2023-06-16 06:38:32',	'2023-06-16 06:38:32',	'Aosta, Aosta Valley, 11100, Italy',	'',	'aosta-aosta-valley-11100-italy',	'',	NULL,	4),
('2280bdec-ac4b-4ed5-a565-d42bd15eda37',	'47255312',	'8370ec26-591f-40ab-8746-10fff8d27991',	'2024-08-30 09:40:59',	'2024-08-30 09:40:59',	'Rethymnon, Municipality of Rethymnon, Unitat perifèrica de Réthimno, Perifèria de Creta, Creta, 741 31, Grècia',	'',	'rethymnon-municipality-of-rethymnon-unitat-periferica-de-rethimno-periferia-de-creta-creta-741-31-grecia',	'',	NULL,	4),
('2e27a279-736a-4aa0-acf7-47a05eac873b',	'47181774',	'8370ec26-591f-40ab-8746-10fff8d27991',	'2024-08-30 09:38:26',	'2024-08-30 09:38:26',	'Downtown Hersonissos, 177, Ελευθερίου Βενιζέλου, Limenas Chersonisou, Chersonisos Municipal Unit, Municipi de Khersónissos, Unitat perifèrica Iràklio, Perifèria de Creta, Creta, 700 14, Grècia',	'',	'downtown-hersonissos-177-eleutheriou-benizelou-limenas-chersonisou-chersonisos-municipal-unit-municipi-de-khersonissos-unitat-periferica-diraklio-periferia-de-creta-creta-700-14-grecia',	'',	NULL,	1),
('6787cf16-0440-45d1-9d93-b3dd72abaf97',	'368734843',	'8370ec26-591f-40ab-8746-10fff8d27991',	'2024-08-30 09:40:04',	'2024-08-30 09:40:04',	'Chania, Municipi de Khania, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, 731 36, Grècia',	'',	'chania-municipi-de-khania-unitat-periferica-de-khania-periferia-de-creta-creta-731-36-grecia',	'',	NULL,	4),
('74fb4883-09fa-4bc7-a0ab-4c486fcf6b93',	'47083486',	'8370ec26-591f-40ab-8746-10fff8d27991',	'2024-08-30 09:42:21',	'2024-08-30 09:42:21',	'Samaria Gorge entrance, Municipi de Plataniàs, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, Grècia',	'',	'samaria-gorge-entrance-municipi-de-platanias-unitat-periferica-de-khania-periferia-de-creta-creta-grecia',	'',	NULL,	3),
('b4703887-5b01-4854-83c8-5d9c6d9ef15d',	'79500372',	'8370ec26-591f-40ab-8746-10fff8d27991',	'2024-08-30 09:45:46',	'2024-08-30 09:45:46',	'AIRPORT, Ikarou Avenue, Community of Nea Alikarnassos, Nea Alikarnassos Municipal Unit, Municipality of Heraklion, Unitat perifèrica Iràklio, Perifèria de Creta, Creta, 716 01, Grècia',	'',	'airport-ikarou-avenue-community-of-nea-alikarnassos-nea-alikarnassos-municipal-unit-municipality-of-heraklion-unitat-periferica-diraklio-periferia-de-creta-creta-716-01-grecia',	'',	NULL,	2),
('c95a1293-851c-4085-be30-6129926fc770',	'50620170',	'8370ec26-591f-40ab-8746-10fff8d27991',	'2024-08-30 09:44:42',	'2024-08-30 09:44:42',	'Elafonissi, Elafonisi Beach, Municipi de Kissamos, Unitat perifèrica de Khanià, Perifèria de Creta, Creta, Grècia',	'',	'elafonissi-elafonisi-beach-municipi-de-kissamos-unitat-periferica-de-khania-periferia-de-creta-creta-grecia',	'',	NULL,	3),
('de9bfb1e-fc2b-4c56-8f54-04c1661064c2',	'47182068',	'8370ec26-591f-40ab-8746-10fff8d27991',	'2024-08-30 09:43:00',	'2024-08-30 09:43:00',	'Spinalonga, Community of Elounta, Agios Nikolaos Municipal Unit, Municipality of Agios Nikolaos, Unitat perifèrica de Lassithi, Perifèria de Creta, Creta, 721 00, Grècia',	'',	'spinalonga-community-of-elounta-agios-nikolaos-municipal-unit-municipality-of-agios-nikolaos-unitat-periferica-de-lassithi-periferia-de-creta-creta-721-00-grecia',	'',	NULL,	3),
('21582bee-5c62-426f-a8d6-cdd55bb19385',	'107544054',	'b10b8ae9-451c-4195-97c3-8c99dc63c123',	'2024-08-30 10:02:46',	'2024-08-30 10:02:46',	'La mer de glaces, Avenue de Aiguille du Midi, Les Favrands, Chamonix-Mont-Blanc, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74400, França',	'',	'la-mer-de-glaces-avenue-de-laiguille-du-midi-les-favrands-chamonix-mont-blanc-bonneville-alta-savoia-alvernia-roine-alps-franca-metropolitana-74400-franca',	'Visiat a la glaçera',	NULL,	3),
('2161a020-b0c3-44ea-88ea-19602223fadb',	'75129326',	'b10b8ae9-451c-4195-97c3-8c99dc63c123',	'2024-08-30 10:00:50',	'2024-08-30 10:00:50',	'Aiguille du Midi, Chamonix-Mont-Blanc, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74400, França',	'',	'aiguille-du-midi-chamonix-mont-blanc-bonneville-alta-savoia-alvernia-roine-alps-franca-metropolitana-74400-franca',	'Visita al punt mes alt',	NULL,	3),
('248ad628-5a87-43c3-992e-173e043c9e90',	'107354879',	'b10b8ae9-451c-4195-97c3-8c99dc63c123',	'2024-08-30 09:56:27',	'2024-08-30 09:56:27',	'Annecy, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, França',	'',	'annecy-alta-savoia-alvernia-roine-alps-franca-metropolitana-franca',	'',	NULL,	1),
('3418cc74-ca56-4a97-91e3-56a5c18a498d',	'107551267',	'b10b8ae9-451c-4195-97c3-8c99dc63c123',	'2024-08-30 10:01:55',	'2024-08-30 10:01:55',	'Refuge du Lac Blanc, Tour du Pays du Mont-Blanc, Chamonix-Mont-Blanc, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74400, França',	'',	'refuge-du-lac-blanc-tour-du-pays-du-mont-blanc-chamonix-mont-blanc-bonneville-alta-savoia-alvernia-roine-alps-franca-metropolitana-74400-franca',	'Caminata',	NULL,	3),
('98ed01d2-6945-4a58-87b4-1d8cf759112e',	'107120402',	'b10b8ae9-451c-4195-97c3-8c99dc63c123',	'2024-08-30 09:57:16',	'2024-08-30 09:57:16',	'Les Houches, Bonneville, Alta Savoia, Alvèrnia-Roine-Alps, França metropolitana, 74310, França',	'',	'les-houches-bonneville-alta-savoia-alvernia-roine-alps-franca-metropolitana-74310-franca',	'Allotjament per la cursa',	NULL,	1),
('b11c070a-9d56-4a9e-b31e-f083a2816a78',	'75293901',	'b10b8ae9-451c-4195-97c3-8c99dc63c123',	'2024-08-30 09:59:15',	'2024-08-30 09:59:15',	'Orsières, Entremont, Valais, Suïssa',	'',	'orsieres-entremont-valais-suissa',	'Sortida',	NULL,	4),
('0aa4c941-90f9-478b-9982-a219059cac00',	'260852438',	'3e778d33-24d9-49e0-93d3-5a8c630d5520',	'2026-04-04 10:41:57',	'2026-04-04 10:41:57',	'Eilean Donan, Totaig, Dornie, Consell de Highland, Escòcia, Regne Unit',	'',	'eilean-donan-totaig-dornie-consell-de-highland-escocia-regne-unit',	'',	NULL,	3),
('347d9085-e7df-4ea7-a80a-1885e2e19598',    '253832841',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:45:36',  '2026-04-04 10:45:36',  'Inverness, Consell de Highland, Escòcia, IV1 1AN, Regne Unit', '', 'inverness-consell-de-highland-escocia-iv1-1an-regne-unit', '', NULL,   4),
('5d171f71-903f-4916-9864-11c0116d513e',    '250746677',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:46:29',  '2026-04-04 10:46:29',  'Elgin, Moray, Escòcia, IV30 1EA, Regne Unit',  '', 'elgin-moray-escocia-iv30-1ea-regne-unit',  'Poble molt bonic', NULL,   4),
('6581ac26-f49a-4b89-84e7-5e2d29515bf5',    '253103163',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:43:01',  '2026-04-04 10:43:01',  'Portree, Consell de Highland, Escòcia, IV51 9EH, Regne Unit',  '', 'portree-consell-de-highland-escocia-iv51-9eh-regne-unit',  '', NULL,   4),
('888d030d-084d-49f0-8a12-6318bdebf845',    '249452898',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:43:40',  '2026-04-04 10:43:40',  'Oban, Argyll and Bute, Escòcia, PA34 4AT, Regne Unit', '', 'oban-argyll-and-bute-escocia-pa34-4at-regne-unit', '', NULL,   4),
('8cca5436-b943-4967-bc5a-f07cc60d93ee',    '253794600',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:45:05',  '2026-04-04 10:45:05',  'Illa de Mull, Argyll and Bute, Escòcia, Regne Unit',   '', 'illa-de-mull-argyll-and-bute-escocia-regne-unit',  '', NULL,   4),
('9f35ef08-d401-49b8-a47e-3911c1334805',    '254099028',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:45:56',  '2026-04-04 10:45:56',  'Perth, Perth i Kinross, Escòcia, PH1 5TZ, Regne Unit', '', 'perth-perth-i-kinross-escocia-ph1-5tz-regne-unit', 'Poble molt bonic', NULL,   4),
('ac97f8da-b692-46ee-8888-3fe1f58bcd27',    '253005868',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:44:38',  '2026-04-04 10:44:38',  'Edimburg, Escòcia, Regne Unit',    '', 'edimburg-escocia-regne-unit',  '', NULL,   4),
('bce50177-6029-4f7a-bb77-9e3c0b259ed2',    '254852643',    '3e778d33-24d9-49e0-93d3-5a8c630d5520', '2026-04-04 10:44:16',  '2026-04-04 10:44:16',  'Glasgow, Glasgow City, Escòcia, G2 1AL, Regne Unit',   '', 'glasgow-glasgow-city-escocia-g2-1al-regne-unit',   '', NULL,   4);

INSERT INTO travelGuuid.migration_versions (version, executed_at) VALUES
('20180120123701', '2018-01-20 12:37:01'),
('20180203182434', '2018-02-03 18:24:34'),
('20181021183748', '2018-10-21 18:37:48'),
('20260402120000', '2026-04-02 12:00:00');

