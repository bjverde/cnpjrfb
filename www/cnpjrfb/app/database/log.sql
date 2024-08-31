CREATE TABLE system_change_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp,
    login varchar(256),
    tablename varchar(256),
    primarykey varchar(256),
    pkvalue varchar(256),
    operation varchar(256),
    columnname varchar(256),
    oldvalue text,
    newvalue text,
    access_ip varchar(256),
    transaction_id varchar(256), 
    log_trace text,
    session_id varchar(256),
    class_name varchar(256),
    php_sapi varchar(256),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_sql_log (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp,
    login varchar(256),
    database_name varchar(256),
    sql_command text,
    statement_type varchar(256),
    access_ip varchar(45),
    transaction_id varchar(256),
    log_trace text,
    session_id varchar(256),
    class_name varchar(256),
    php_sapi varchar(256),
    request_id varchar(256),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2)
);
CREATE TABLE system_access_log (
    id INTEGER PRIMARY KEY NOT NULL,
    sessionid varchar(256),
    login varchar(256),
    login_time timestamp,
    login_year varchar(4),
    login_month varchar(2),
    login_day varchar(2),
    logout_time timestamp,
    impersonated char(1),
    access_ip varchar(45),
    impersonated_by varchar(200)
);

CREATE TABLE system_request_log (
    id INTEGER PRIMARY KEY NOT NULL,
    endpoint varchar(4096),
    logdate varchar(256),
    log_year varchar(4),
    log_month varchar(2),
    log_day varchar(2),
    session_id varchar(256),
    login varchar(256),
    access_ip varchar(256),
    class_name varchar(256),
    class_method varchar(256),
    http_host varchar(256),
    server_port varchar(256),
    request_uri text,
    request_method varchar(256),
    query_string text,
    request_headers text,
    request_body text,
    request_duration INT
);

CREATE TABLE system_access_notification_log (
    id INTEGER PRIMARY KEY NOT NULL,
    login varchar(256),
    email varchar(256),
    ip_address varchar(256),
    login_time varchar(256)
);

CREATE INDEX sys_change_log_login_idx ON system_change_log(login);
CREATE INDEX sys_change_log_date_idx ON system_change_log(logdate);
CREATE INDEX sys_change_log_year_idx ON system_change_log(log_year);
CREATE INDEX sys_change_log_month_idx ON system_change_log(log_month);
CREATE INDEX sys_change_log_day_idx ON system_change_log(log_day);
CREATE INDEX sys_change_log_class_idx ON system_change_log(class_name);
CREATE INDEX sys_change_log_table_idx ON system_change_log(tablename);

CREATE INDEX sys_sql_log_login_idx ON system_sql_log(login);
CREATE INDEX sys_sql_log_date_idx ON system_sql_log(logdate);
CREATE INDEX sys_sql_log_database_idx ON system_sql_log(database_name);
CREATE INDEX sys_sql_log_class_idx ON system_sql_log(class_name);
CREATE INDEX sys_sql_log_year_idx ON system_sql_log(log_year);
CREATE INDEX sys_sql_log_month_idx ON system_sql_log(log_month);
CREATE INDEX sys_sql_log_day_idx ON system_sql_log(log_day);

CREATE INDEX sys_access_log_login_idx ON system_access_log(login);
CREATE INDEX sys_access_log_year_idx ON system_access_log(login_year);
CREATE INDEX sys_access_log_month_idx ON system_access_log(login_month);
CREATE INDEX sys_access_log_day_idx ON system_access_log(login_day);

CREATE INDEX sys_request_log_login_idx ON system_request_log(login);
CREATE INDEX sys_request_log_date_idx ON system_request_log(logdate);
CREATE INDEX sys_request_log_year_idx ON system_request_log(log_year);
CREATE INDEX sys_request_log_month_idx ON system_request_log(log_month);
CREATE INDEX sys_request_log_day_idx ON system_request_log(log_day);
CREATE INDEX sys_request_log_class_idx ON system_request_log(class_name);
CREATE INDEX sys_request_log_method_idx ON system_request_log(class_method);

CREATE INDEX sys_access_notification_log_login_idx ON system_access_notification_log(login);

