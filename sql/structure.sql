# create database if not exists iritec character set utf8 collate utf8_general_ci;
# create user 'iritec'@'localhost' identified by '12345';
# grant all on iritec.* to 'iritec'@'localhost';

drop table if exists realty_offer;
create table realty_offer (
    id int not null primary key auto_increment,
    agent_id int not null references realty_sales_agent(id),
    internal_id int not null,
    type varchar(255) null,
    property_type varchar(255) null,
    category varchar(255) null,
    url varchar(255) null,
    creation_date timestamp not null,
    last_update_date timestamp null,
    expire_date timestamp,
    payed_adv boolean not null default false,
    manually_added boolean not null default false,

    price_currency varchar(3) not null,
    price_value decimal(15,4) not null,
    price_unit varchar(50) null,
    price_period varchar(50) null,

    not_for_agents boolean not null default false,
    haggle boolean not null default false,
    mortgage boolean not null default false,
    prepayment boolean not null default false,
    rent_pledge boolean not null default false,
    agent_fee decimal(15,4) not null default 0,
    with_pets boolean not null default false,
    with_children boolean not null default false,

    renovation varchar(255) not null,
    description text null,
    area_unit varchar(20) null,
    area_value decimal(15,4) null,
    living_space_unit varchar(20) null,
    living_space_value decimal(15,4) null,
    kitchen_space_unit varchar(20) null,
    kitchen_space_value decimal(15,4) null,
    lot_area_unit varchar(20) null,
    lot_area_value decimal(15,4) null,
    lot_type varchar(50) null,
    new_flat boolean not null default false,
    rooms integer null,
    rooms_offered integer null,
    open_plan boolean not null default false,
    rooms_type boolean not null default false,
    phone boolean not null default false,
    internet boolean not null default false,
    room_furniture boolean not null default false,
    kitchen_furniture boolean not null default false,
    television boolean not null default false,
    washing_machine boolean not null default false,
    refrigerator boolean not null default false,
    balcony varchar(50) null,
    bathroom_unit varchar(50) null,
    floor_covering boolean not null default false,
    window_view boolean not null default false,
    floor integer null,


    floors_total integer null,
    building_name varchar(255) null,
    building_type varchar(255) null,
    building_series varchar(255) null,
    building_state varchar(255) null,
    built_year integer null,
    ready_quarter tinyint null,
    lift boolean not null default false,
    rubbish_chute boolean not null default false,
    is_elite boolean not null default false,
    parking boolean not null default false,
    alarm boolean not null default false,
    ceiling_height decimal(4,2),


    pmg boolean not null default false,
    toilet varchar(50) null,
    shower varchar(50) null,
    kitchen boolean not null default false,
    pool boolean not null default false,
    billiard boolean not null default false,
    sauna boolean not null default false,
    heating_supply boolean not null default false,
    water_supply boolean not null default false,
    sewerage_supply boolean not null default false,
    electricity_supply boolean not null default false,
    gas_supply boolean not null default false
    
) engine="InnoDB" charset="utf8";

drop table if exists realty_sales_agent;
create table realty_sales_agent (
    id int not null primary key auto_increment,
    name varchar(255) not null,
    phone varchar(255) not null,
    category varchar(255) null,
    organization varchar(255) null,
    agency_id varchar(255) null,
    url varchar(255) null,
    email varchar(255) null,
    partner varchar(255) null,
    unique key(phone),
    unique key(email)
) engine="InnoDB" charset="utf8";

drop table if exists realty_offer_location;
create table realty_offer_location (
    id int not null primary key auto_increment,
    offer_id int not null references realty_offer(id),
    country varchar(255) null,
    region varchar(255) null,
    district varchar(255) null,
    locality_name varchar(255) null,
    sub_locality_name varchar(255) null,
    non_admin_sub_locality varchar(255) null,
    address varchar(255) null,
    direction varchar(255) null,
    distance decimal(15,4) null,
    latitude decimal(15,4) null,
    longitude decimal(15,4) null,
    metro_name varchar(255) null,
    metro_time_on_foot decimal(15,4) null,
    metro_time_on_transport decimal(15,4) null,
    railway_station varchar(255) null

) engine="InnoDB" charset="utf8";


drop table if exists realty_offer_image;
create table realty_offer_image (
    id int not null primary key auto_increment,
    offer_id int not null references realty_offer(id),
    url varchar(512) not null
) engine="InnoDB" charset="utf8";