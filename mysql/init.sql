SET NAMES utf8mb4;
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

CREATE TABLE recipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    ingredients TEXT NOT NULL,
    instructions TEXT NOT NULL,
    cooking_time INT NOT NULL DEFAULT 30,
    emoji VARCHAR(20) NOT NULL DEFAULT '🍲',
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

INSERT INTO categories (name) VALUES
('Món chính'),
('Món canh'),
('Món chay'),
('Tráng miệng');

INSERT INTO recipes
(category_id, name, description, ingredients, instructions, cooking_time, emoji)
VALUES
(1, 'Phở bò', 'Món phở truyền thống Việt Nam',
'Bánh phở 500g;Thịt bò 300g;Xương bò 1kg;Hành tây;Gừng;Gia vị',
'Hầm xương bò lấy nước dùng;Nướng hành và gừng;Luộc bánh phở;Thái thịt bò;Chan nước dùng và thưởng thức',
90, '🍜'),

(1, 'Cơm rang trứng', 'Món ăn đơn giản cho bữa sáng',
'Cơm nguội 2 bát;Trứng gà 2 quả;Hành lá;Dầu ăn;Gia vị',
'Đánh đều trứng;Phi thơm hành;Cho cơm vào đảo;Thêm trứng và gia vị;Rang đến khi cơm tơi',
20, '🍚'),

(2, 'Canh chua cá', 'Canh chua thanh mát',
'Cá 500g;Cà chua 2 quả;Dứa 1/2 quả;Me chua;Rau thơm',
'Rửa sạch cá;Đun nước với me và dứa;Cho cá vào nấu;Thêm cà chua và gia vị;Rắc rau thơm',
35, '🍲'),

(3, 'Đậu hũ sốt cà chua', 'Món chay dễ nấu',
'Đậu hũ 3 miếng;Cà chua 3 quả;Hành lá;Dầu ăn;Gia vị',
'Cắt đậu thành miếng;Chiên vàng đậu;Xào cà chua thành sốt;Cho đậu vào đun nhỏ lửa;Rắc hành lá',
25, '🥘'),

(4, 'Bánh flan', 'Món tráng miệng mềm mịn',
'Trứng gà 4 quả;Sữa tươi 400ml;Đường 100g;Vani',
'Thắng đường làm caramel;Đánh trứng cùng sữa;Lọc hỗn hợp;Đổ vào khuôn;Hấp cách thủy đến khi chín',
50, '🍮'),

(4, 'Chè đậu xanh', 'Món chè ngọt thanh',
'Đậu xanh 200g;Đường 100g;Nước cốt dừa;Nước lọc',
'Ngâm đậu xanh;Nấu đậu đến khi mềm;Thêm đường;Khuấy đều;Ăn cùng nước cốt dừa',
40, '🍨');
