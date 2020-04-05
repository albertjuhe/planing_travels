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
('f37671ca-e49f-41f8-9c20-d334452cd452',	'EitTdHIuIGRlaSBTZWx2b2xpbmksIE1vbnRlcmlnZ2lvbmkgU0ksIEl0YWx5Ii4qLAoUChIJeWc5SYwyKhMRNJgZgIFAsMASFAoSCakdtFRPLioTEfCl5eOQLAgE',	'9c7299d3-665b-4469-ba47-9020c38e91d7',	'2020-04-05 17:00:47',	'2020-04-05 17:00:47',	'Str. dei Selvolini, Monteriggioni SI, Italia',	'http://www.agriturismolegallozzole.it/',	'str-dei-selvolini-monteriggioni-si-italia',	'Turisme rural',	NULL,	1);

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
('EitTdHIuIGRlaSBTZWx2b2xpbmksIE1vbnRlcmlnZ2lvbmkgU0ksIEl0YWx5Ii4qLAoUChIJeWc5SYwyKhMRNJgZgIFAsMASFAoSCakdtFRPLioTEfCl5eOQLAgE',	'MonteriggioniAllotjament Agriturismo Le Gallozzole',	'2020-04-05 17:00:47',	'2020-04-05 17:00:47',	'{\"placeAddress\":\"Str. dei Selvolini, Monteriggioni SI, Italia\",\"IdType\":\"1\",\"link\":\"http://www.agriturismolegallozzole.it/\",\"comment\":\"Turisme rural\",\"latitude\":43.4016385,\"longitude\":11.2965372,\"place_id\":\"EitTdHIuIGRlaSBTZWx2b2xpbmksIE1vbnRlcmlnZ2lvbmkgU0ksIEl0YWx5Ii4qLAoUChIJeWc5SYwyKhMRNJgZgIFAsMASFAoSCakdtFRPLioTEfCl5eOQLAgE\",\"address\":\"MonteriggioniAllotjament Agriturismo Le Gallozzole\",\"travel\":\"9c7299d3-665b-4469-ba47-9020c38e91d7\",\"user\":\"4\",\"currentMark\":null}',	43.40163850,	11.29653720,	0.00000000,	0.00000000,	0.00000000,	0.00000000);

