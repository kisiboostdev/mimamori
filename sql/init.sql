BEGIN;
DROP TABLE IF EXISTS departments;
DROP TABLE IF EXISTS members;
DROP TABLE IF EXISTS devices;
DROP TABLE IF EXISTS expression_reports;

CREATE TABLE departments (
  id   serial PRIMARY KEY,
  name text NOT NULL
);

CREATE TABLE members (
  id   serial PRIMARY KEY,
  name text NOT NULL,
  twitter_uname text,
  department_id integer NOT NULL
);

CREATE TABLE devices (
  id   serial PRIMARY KEY,
  uuid text NOT NULL UNIQUE,
  member_id integer NOT NULL
);

CREATE TABLE expression_reports (
  id   serial PRIMARY KEY,
  member_id integer NOT NULL,
  expression integer NOT NULL,
  score integer NOT NULL,
  degree integer NOT NULL,
  regist_time timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
);

COMMIT;
