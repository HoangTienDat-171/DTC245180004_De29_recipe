# DE 29 - WEBSITE CONG THUC NAU AN

Mon hoc: Trien khai va Quan tri He thong Phan mem

MSSV: DTC245180004

## 1. Gioi thieu

Website chia se cong thuc nau an, cho phep:
- Xem danh sach mon an
- Tim kiem mon an
- Loc theo danh muc
- Xem nguyen lieu va cach nau

Website duoc viet bang PHP thuan, su dung MySQL.

## 2. Cong nghe

- PHP 8.3 + Apache
- MySQL 8.0
- phpMyAdmin
- Nginx Reverse Proxy
- Docker + Docker Compose
- Prometheus + Grafana
- cAdvisor
- Nginx Exporter
- MySQL Exporter
- Loki + Promtail

## 3. Kien truc he thong

Nguoi dung
    |
    v
Nginx Reverse Proxy
    |
    v
PHP Website
    |
    v
MySQL Database

phpMyAdmin --> MySQL

Exporters + cAdvisor --> Prometheus --> Grafana

Docker Logs --> Promtail --> Loki --> Grafana

Tat ca dich vu chay bang Docker Compose.

## 4. Cau truc project

app/                 Ma nguon website PHP
mysql/               SQL khoi tao va cau hinh
nginx/               Cau hinh Reverse Proxy
monitoring/          Prometheus va Grafana
logging/             Loki va Promtail
scripts/             Script ho tro cai dat
compose.yaml         Ung dung va Database
compose.monitoring.yaml
compose.logging.yaml
Dockerfile
.env.example

## 5. Yeu cau

- Ubuntu Linux
- Docker Engine
- Docker Compose v2
- Git
- RAM khuyen nghi: 4 GB tro len
- Dung luong dia trong phu hop

## 6. Cai dat

Clone repository:

git clone https://github.com/HoangTienDat-171/DTC245180004_De29_recipe.git

cd DTC245180004_De29_recipe

Tao file moi truong:

cp .env.example .env

Sua .env va thay cac mat khau mau bang mat khau manh.
Moi mat khau nen khac nhau.

Khuyen nghi tao mat khau:

openssl rand -hex 24

Bao ve file:

chmod 600 .env

Khoi chay MySQL, website, Nginx va phpMyAdmin:

sudo docker compose -f compose.yaml up -d --build

Tao tai khoan giam sat MySQL:

bash scripts/setup-monitor-user.sh

Khoi chay toan bo he thong:

sudo docker compose \
  -f compose.yaml \
  -f compose.monitoring.yaml \
  -f compose.logging.yaml \
  up -d

Kiem tra:

sudo docker compose \
  -f compose.yaml \
  -f compose.monitoring.yaml \
  -f compose.logging.yaml \
  ps

## 7. Truy cap

Website:
http://IP_MAY_AO

Cac cong quan tri duoc gioi han tai localhost
cua Ubuntu va co the truy cap qua SSH tunnel.

Vi du tu Windows:

ssh -N \
  -L 18081:127.0.0.1:8081 \
  -L 19090:127.0.0.1:9090 \
  -L 13001:127.0.0.1:3001 \
  USER@IP_MAY_AO

Sau khi tao tunnel:

phpMyAdmin: http://127.0.0.1:18081
Prometheus: http://127.0.0.1:19090
Grafana: http://127.0.0.1:13001

phpMyAdmin:
- Username: recipe_user
- Password: MYSQL_PASSWORD trong .env

Grafana:
- Username: admin
- Password: GRAFANA_ADMIN_PASSWORD trong .env

## 8. Prometheus

Cac target:

- prometheus:9090
- cadvisor:8080
- nginx-exporter:9113
- mysql-exporter:9104

PromQL kiem tra:

mysql_up

nginx_up

up{job="cadvisor"}

CPU va RAM duoc thu thap bang cAdvisor.
Can loc dung container khi tinh tong tai nguyen
de tranh cong trung cac nhom cgroup.

## 9. Grafana

Datasource Prometheus:

http://prometheus:9090

Datasource Loki:

http://loki:3100

Dashboard JSON, neu duoc xuat, nam trong:

monitoring/grafana/dashboards/

Co the import JSON thu cong vao Grafana.

## 10. Loki va Promtail

Promtail thu thap log Docker va gui den Loki.

LogQL 1 - Xem log du an:

{compose_project="recipe29"}

LogQL 2 - Loc GET Nginx:

{compose_service="nginx"} |= "GET"

LogQL 3 - Loc HTTP 404:

{compose_service="nginx"} |= " 404 "

Thuc hien cac truy van trong Grafana Explore.

## 11. Hardening

Cac bien phap da cau hinh:

1. Nginx su dung image unprivileged.
2. MySQL khong publish cong 3306 ra host.
3. Mang backend duoc cau hinh internal.
4. Su dung mat khau manh, tach biet.
5. File .env khong duoc commit len GitHub.
6. Nginx co security headers.
7. phpMyAdmin, Grafana, Prometheus, Loki
   chi publish cong quan tri tren 127.0.0.1.
8. MySQL co user ung dung va user monitoring.

Luu y:
- cAdvisor hien su dung privileged mode.
- Promtail co quyen truy cap Docker socket.
- Cau hinh phuc vu VM thuc hanh co lap.
- Chua phai cau hinh production hoan chinh.
- HTTPS chua duoc cau hinh; Nginx dang
  su dung security headers theo yeu cau de bai.

## 12. GitHub

Commit 1:
Website + MySQL + phpMyAdmin + Nginx

Commit 2:
Prometheus + Grafana + Exporters

Commit 3:
Loki + Promtail + Hardening + Documentation

## 13. Kiem tra va dung he thong

Kiem tra toan bo:

sudo docker compose \
  -f compose.yaml \
  -f compose.monitoring.yaml \
  -f compose.logging.yaml \
  ps

Dung he thong, giu lai du lieu:

sudo docker compose \
  -f compose.yaml \
  -f compose.monitoring.yaml \
  -f compose.logging.yaml \
  down

KHONG su dung down -v neu muon giu database,
dashboard va du lieu giám sat.
