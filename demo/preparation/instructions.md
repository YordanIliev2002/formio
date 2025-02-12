## First setup the XAMPP and DB
## Insert the mock data into the DB
all passwords are 'pass'
```
SET NAMES utf8mb4;
INSERT INTO `users` VALUES
('10000','Teacher Teacherson','$2y$12$knjyfSOZPcG8iuGUhem9vuHL4QaGFYecBqInEuIfW/1DlncK8HXT6', 'teacher@abv.bg'),
('30001','Иван Иванов','$2y$12$J38cacoXdh.oO5SB9UA2v.P8Tw1PDbNGMJJ8YUIin5v8mIXPApVtC', 'ivan@abv.bg'),
('30002','Петър Петров','$2y$12$jQon4A6vYnAp2T3NVvkEdOFLyHczWDDZEjjl/Wbjp4ZONuUTOxRwi', 'petar@abv.bg'),
('30003','Мария Мариева','$2y$12$WiMVXBQRr1ZNFutVD0qifeZQ80nP6txcFbNvFMwiSivo0XF2ePeIy', 'maria@abv.bg'),
('viki-321','Viktoria Nedelcheva','$2y$12$iz.ckJwXSjNDdE8KjlmilOI6XqENLIbqdU1HfOBV2rrsXHn1ojRlO', 'viki@gmail.com'),
('yordan-123','Yordan Iliev','$2y$12$/t6T86xBrGQ3vNnsSqWzf.5J1pxG0nqs68ynOsJ1b2P7rIqajpos6', 'yordan@gmail.com');

INSERT INTO `forms` VALUES
('163a748f-dcf8-11ef-a4fa-0242ac110002','10000','2025-01-27 21:45:55','{\"theme\": \"blue\", \"title\": \"Application form for HoM\", \"fields\": [{\"name\": \"name\", \"type\": \"text\", \"label\": \"Name\", \"required\": true}, {\"name\": \"email\", \"type\": \"text\", \"label\": \"Email\", \"required\": true}, {\"name\": \"profile_picture\", \"type\": \"file\", \"label\": \"Profile picture\", \"fileType\": \"image/*\", \"required\": false}, {\"name\": \"cv\", \"type\": \"file\", \"label\": \"CV\", \"fileType\": \".pdf\", \"required\": true}], \"description\": \"Apply for our new position as a Head of Marketing\"}'),
('a600169d-dcf6-11ef-a4fa-0242ac110002','10000','2025-01-27 21:35:37','{\"title\": \"Контролно по математика за трети клас\", \"fields\": [{\"name\": \"question-1\", \"type\": \"text\", \"label\": \"Колко е 2 + 2 * 3?\", \"required\": true}, {\"name\": \"question-1\", \"type\": \"text\", \"label\": \"Колко е 2 * 17?\", \"required\": true}, {\"name\": \"question-3\", \"type\": \"multiple_choice\", \"label\": \"Кое е най-голямото число?\", \"choices\": [\"5\", \"2\", \"-100\", \"99\", \"3\"], \"required\": true}, {\"name\": \"question-3\", \"type\": \"multiple_choice\", \"label\": \"Кой знак седи тук: 2 _ 3 = 6\", \"choices\": [\"+\", \"-\", \"*\", \"/\"], \"required\": true}, {\"name\": \"question-5\", \"type\": \"textarea\", \"label\": \"Опишете как ще сметнете 17 + 26 + 12\", \"required\": true}, {\"name\": \"question-5\", \"type\": \"textarea\", \"label\": \"Ако Иван има 5 жаби, и Петър му даде двойно повече, колко жаби има Иван? Опишете с уравнение.\", \"required\": true}], \"accessCode\": \"трети-б-клас\", \"description\": \"Успех!\"}');

INSERT INTO `invites` VALUES
('163a748f-dcf8-11ef-a4fa-0242ac110002','viki-321',0),
('163a748f-dcf8-11ef-a4fa-0242ac110002','yordan-123',0),
('a600169d-dcf6-11ef-a4fa-0242ac110002','30001',0),
('a600169d-dcf6-11ef-a4fa-0242ac110002','30002',1),
('a600169d-dcf6-11ef-a4fa-0242ac110002','30003',0);

INSERT INTO `responses` VALUES
('a600169d-dcf6-11ef-a4fa-0242ac110002','30002','2025-01-27 21:42:58','{\"question-1\": \"34\", \"question-3\": \"*\", \"question-5\": \"5 + 2 * 5 = 15\"}');


```
## Copy the pdf file to the uploads folder

## Live demo
- Login into `10000`. Show the dashboard. Show the statistics for both forms.
- Show both forms.
- Login into `yordan-123`. Submit the form.
- Login into `30001`. Submit the form.
- Login back into `10000`. Show the statistics and the exports.