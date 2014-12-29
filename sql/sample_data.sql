begin ;
truncate departments;
truncate members;
truncate devices;

insert into departments (name) values ('開発部');
insert into departments (name) values ('企画部');

insert into members (name, department_id, twitter_uname) values('福井', 1, 'kisiboost');
insert into members (name, department_id, twitter_uname) values('田中', 1, null);
insert into members (name, department_id, twitter_uname) values('山田', 2, 'kisiboost');

insert into devices( uuid, member_id) values ('12345', 1);
insert into devices( uuid, member_id) values ('67890', 2);

--initController.phpで対応
insert into expression_reports (member_id , expression, score, degree) values (1, 1, 50, 60);
insert into expression_reports (member_id , expression, score, degree) values (1, 1, 60, 70);
insert into expression_reports (member_id , expression, score, degree) values (1, 1, 40, 30);
insert into expression_reports (member_id , expression, score, degree) values (2, 1, 20, 10);
insert into expression_reports (member_id , expression, score, degree) values (2, 1, 40, 40);

commit;
