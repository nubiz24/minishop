# Nhật Ký Phát Triển

Dự án: website bán hàng nhỏ dùng Laravel, Vue/Inertia, PostgreSQL và Sail.

## 2026-07-09

- Refactor backend theo Repository Design Pattern để controller không gọi trực tiếp các câu truy vấn Eloquent như `Product::query()`, `Order::query()`, `User::query()`.
- Tạo các interface repository trong `app/Repositories/Contracts`:
  - `ProductRepositoryInterface`
  - `OrderRepositoryInterface`
  - `UserRepositoryInterface`
- Tạo các lớp triển khai bằng Eloquent trong `app/Repositories/Eloquent`:
  - `EloquentProductRepository`
  - `EloquentOrderRepository`
  - `EloquentUserRepository`
- Bind interface với implementation trong `app/Providers/AppServiceProvider.php` để Laravel tự inject đúng repository vào controller.
- Chuyển `app/Support/Cart.php` từ helper static sang service dùng dependency injection, nhờ đó giỏ hàng lấy sản phẩm thông qua `ProductRepositoryInterface`.
- Cập nhật các controller để gọi repository thay vì tự viết query trực tiếp trong controller:
  - `ShopController`
  - `CartController`
  - `CheckoutController`
  - `UserOrderController`
  - `Admin/ProductController`
  - `Admin/OrderController`
  - `Admin/UserController`
- Kiểm tra cú pháp PHP bằng `php -l`: tất cả file vừa sửa không có lỗi cú pháp.
- Kiểm tra Laravel boot và route bằng `php artisan route:list`: thành công, hiển thị 69 route.
- Kiểm tra Laravel service container resolve được các controller đã inject repository: thành công.
- Chưa chạy lại được `docker compose exec laravel.test php artisan test` vì Docker Desktop/daemon đang tắt trên máy host.

## 2026-07-06

- Tạo dự án Laravel mới bằng Vue Starter Kit. Starter Kit đã có sẵn Inertia, giao diện đăng ký/đăng nhập, Composer dependencies và NPM dependencies.
- Laravel Installer không cho dùng `--force` khi cài trực tiếp vào thư mục hiện tại, nên dự án được tạo tạm trong thư mục `shop_app`, sau đó chuyển toàn bộ file ra thư mục gốc.
- Kiểm tra phiên bản framework bằng `php artisan --version`: Laravel Framework 13.18.1.
- Lần build frontend đầu tiên bị lỗi vì `npm` không tìm thấy `php` trong Windows PATH. Khi chạy lệnh local cần thêm đường dẫn PHP của Herd Lite trước Composer, Artisan hoặc NPM.
- Cài Laravel Sail với PostgreSQL bằng lệnh `php artisan sail:install --with=pgsql`. Laravel Sail 1.53 tạo file `compose.yaml`, không dùng tên cũ `docker-compose.yml`.
- Cập nhật `.env` để Laravel kết nối tới PostgreSQL service của Sail:
  - `DB_CONNECTION=pgsql`
  - `DB_HOST=pgsql`
  - `DB_USERNAME=sail`
  - `DB_PASSWORD=password`
  - `FORWARD_DB_PORT=5433` để pgAdmin trên Windows kết nối vào PostgreSQL Docker mà không đụng PostgreSQL local ở port `5432`.
- Thêm các bảng chính cho website bán hàng:
  - `users.role` để phân quyền admin/user.
  - `products` để lưu sản phẩm.
  - `orders` và `order_items` để lưu đơn hàng.
- Thêm quan hệ Eloquent để dễ theo dõi luồng dữ liệu:
  - `User` có nhiều `Order`.
  - `Order` thuộc về `User` và có nhiều `OrderItem`.
  - `Product` có nhiều `OrderItem`.
- Thêm middleware `admin`, chỉ cho user có `role = admin` vào khu vực quản trị.
- Thêm dữ liệu mẫu:
  - Admin: `admin@example.com` / `password`
  - User: `user@example.com` / `password`
  - 4 sản phẩm mẫu và 1 đơn hàng mẫu.
- Thêm route phía người dùng cho trang chủ, danh sách sản phẩm, chi tiết sản phẩm, giỏ hàng, thanh toán và trang đặt hàng thành công.
- Thêm route phía admin để quản lý user, sản phẩm và đơn hàng.
- Xây dựng các trang Vue/Inertia cho giao diện shop, giỏ hàng, checkout và admin.
- Thêm chức năng user xem đơn hàng đã đặt:
  - Route `/orders` để xem danh sách đơn hàng của chính user.
  - Route `/orders/{order}` để xem chi tiết đơn hàng.
  - Controller `UserOrderController` chỉ cho user xem đơn có `user_id` của chính họ.
  - Trang Vue `Orders/Index.vue` và `Orders/Show.vue`.
  - Link `Don hang` trong `ShopLayout.vue`.
- Sửa thứ tự migration bằng cách đổi tên file migration để PostgreSQL tạo bảng theo thứ tự: `products`, sau đó `orders`, sau đó `order_items`.
- Lệnh `php artisan test` chạy trên PHP ngoài host bị lỗi vì PHP của Herd Lite chưa có PostgreSQL PDO driver. Khi chạy trong container Sail/Docker thì test pass vì container có `pdo_pgsql`.
- Khởi động service bằng Docker Compose:
  - `laravel.test` chạy app tại `http://localhost:8000`.
  - `pgsql` chạy PostgreSQL trong container tại port `5432`, và được expose ra máy host tại port `5433`.
- Chạy migrate và seed thành công:

  ```bash
  docker compose exec laravel.test php artisan migrate:fresh --seed
  ```

- Chạy test trong container thành công:

  ```bash
  docker compose exec laravel.test php artisan test
  ```

  Kết quả: 39 tests passed.
- Chạy kiểm tra TypeScript thành công:

  ```bash
  npm run types:check
  ```

- Chạy build frontend thành công:

  ```bash
  npm run build
  ```

  Có cảnh báo không ảnh hưởng build từ dependency `reka-ui/@vueuse` về pure annotations.
- Kiểm tra nhanh bằng HTTP:
  - `http://localhost:8000/` trả về `200 OK`.
  - `http://localhost:8000/products` trả về `200 OK`.

## Cách Chạy Dự Án

1. Khởi động container:

   ```bash
   docker compose up -d
   ```

2. Tạo bảng và dữ liệu mẫu:

   ```bash
   docker compose exec laravel.test php artisan migrate:fresh --seed
   ```

3. Mở website:

   ```text
   http://localhost:8000
   ```

4. Tài khoản demo:

   ```text
   Admin: admin@example.com / password
   User:  user@example.com / password
   ```

## File Cần Code Theo Từng Quá Trình

### 1. Cấu hình môi trường và Docker

- `.env`: cấu hình app, database, port chạy app.
- `.env.example`: mẫu cấu hình cho người khác copy khi setup.
- `compose.yaml`: cấu hình Sail/Docker Compose, gồm app Laravel và PostgreSQL.

### 2. Khai báo database

- `database/migrations/0001_01_01_000000_create_users_table.php`: thêm cột `role` cho user.
- `database/migrations/2026_07_06_071455_create_products_table.php`: tạo bảng sản phẩm.
- `database/migrations/2026_07_06_071456_create_orders_table.php`: tạo bảng đơn hàng.
- `database/migrations/2026_07_06_071457_create_order_items_table.php`: tạo bảng chi tiết đơn hàng.

### 3. Model và quan hệ dữ liệu

- `app/Models/User.php`: khai báo role, quan hệ đơn hàng và hàm kiểm tra admin.
- `app/Models/Product.php`: khai báo sản phẩm, casts và quan hệ với chi tiết đơn hàng.
- `app/Models/Order.php`: khai báo đơn hàng, trạng thái và quan hệ.
- `app/Models/OrderItem.php`: khai báo chi tiết đơn hàng.

### 4. Dữ liệu mẫu

- `database/seeders/DatabaseSeeder.php`: tạo tài khoản admin/user, sản phẩm mẫu và đơn hàng mẫu.
- `database/factories/ProductFactory.php`: tạo dữ liệu sản phẩm giả khi cần factory.

### 5. Phân quyền admin

- `app/Http/Middleware/EnsureUserIsAdmin.php`: chặn user thường vào trang admin.
- `bootstrap/app.php`: đăng ký alias middleware `admin`.

### 6. Route

- `routes/web.php`: khai báo toàn bộ route cho shop, giỏ hàng, checkout và admin.

### 7. Controller phía người dùng

- `app/Http/Controllers/ShopController.php`: trang chủ, danh sách sản phẩm, chi tiết sản phẩm.
- `app/Http/Controllers/CartController.php`: thêm, sửa, xóa giỏ hàng.
- `app/Http/Controllers/CheckoutController.php`: tạo đơn hàng, trừ tồn kho, hiển thị trang thành công.
- `app/Http/Controllers/UserOrderController.php`: user xem danh sách và chi tiết đơn hàng của chính mình.
- `app/Support/Cart.php`: helper xử lý dữ liệu giỏ hàng trong session.

### 8. Controller phía admin

- `app/Http/Controllers/Admin/UserController.php`: quản lý user.
- `app/Http/Controllers/Admin/ProductController.php`: quản lý sản phẩm.
- `app/Http/Controllers/Admin/OrderController.php`: quản lý đơn hàng.

### 9. Layout và navigation

- `resources/js/layouts/ShopLayout.vue`: layout public cho trang shop, giỏ hàng và checkout.
- `resources/js/components/AppSidebar.vue`: menu trong khu vực dashboard/admin.
- `resources/js/components/NavUser.vue`: menu user đăng nhập.
- `resources/js/components/AppHeader.vue`: header của layout app mặc định.
- `resources/js/app.ts`: chọn layout phù hợp cho từng nhóm page.

### 10. Trang Vue phía người dùng

- `resources/js/pages/Shop/Home.vue`: trang chủ.
- `resources/js/pages/Shop/Products.vue`: danh sách sản phẩm.
- `resources/js/pages/Shop/ProductDetail.vue`: chi tiết sản phẩm và nút thêm vào giỏ.
- `resources/js/pages/Cart/Show.vue`: giỏ hàng.
- `resources/js/pages/Checkout/Create.vue`: form checkout.
- `resources/js/pages/Checkout/Success.vue`: đặt hàng thành công.
- `resources/js/pages/Orders/Index.vue`: danh sách đơn hàng của user.
- `resources/js/pages/Orders/Show.vue`: chi tiết đơn hàng của user.
- `resources/js/components/ProductCard.vue`: card sản phẩm dùng lại ở nhiều trang.

### 11. Trang Vue phía admin

- `resources/js/pages/Admin/Users/Index.vue`: quản lý user.
- `resources/js/pages/Admin/Products/Index.vue`: danh sách sản phẩm admin.
- `resources/js/pages/Admin/Products/Form.vue`: form thêm/sửa sản phẩm.
- `resources/js/pages/Admin/Orders/Index.vue`: danh sách đơn hàng admin.
- `resources/js/pages/Admin/Orders/Show.vue`: chi tiết đơn hàng admin.

### 12. TypeScript types

- `resources/js/types/auth.ts`: kiểu dữ liệu user đăng nhập.
- `resources/js/types/shop.ts`: kiểu dữ liệu product, cart, order, pagination.
- `resources/js/types/global.d.ts`: khai báo shared props từ Inertia.
- `resources/js/types/index.ts`: export các type dùng chung.

### 13. File không cần sửa khi học chức năng chính

- `vendor/`: thư viện PHP do Composer cài.
- `node_modules/`: thư viện frontend do NPM cài.
- `public/build/`: file build frontend tự sinh.
- `resources/js/actions/`, `resources/js/routes/`, `resources/js/wayfinder/`: file route/action tự sinh bởi Wayfinder.
- `storage/`: file runtime/cache/log của Laravel.

### 14. Refactor Repository Design Pattern

- `app/Repositories/Contracts/ProductRepositoryInterface.php`: khai báo các hàm thao tác dữ liệu sản phẩm.
- `app/Repositories/Contracts/OrderRepositoryInterface.php`: khai báo các hàm thao tác dữ liệu đơn hàng.
- `app/Repositories/Contracts/UserRepositoryInterface.php`: khai báo các hàm thao tác dữ liệu user.
- `app/Repositories/Eloquent/EloquentProductRepository.php`: triển khai repository sản phẩm bằng Eloquent.
- `app/Repositories/Eloquent/EloquentOrderRepository.php`: triển khai repository đơn hàng bằng Eloquent.
- `app/Repositories/Eloquent/EloquentUserRepository.php`: triển khai repository user bằng Eloquent.
- `app/Providers/AppServiceProvider.php`: bind interface với implementation.
- `app/Support/Cart.php`: lấy dữ liệu sản phẩm trong giỏ hàng thông qua repository.
- `app/Http/Middleware/HandleInertiaRequests.php`: lấy số lượng giỏ hàng qua service `Cart`.
- `app/Http/Controllers/ShopController.php`: lấy danh sách sản phẩm qua repository, chi tiết sản phẩm vẫn dùng route model binding của Laravel.
- `app/Http/Controllers/CartController.php`: xử lý giỏ hàng qua service `Cart`.
- `app/Http/Controllers/CheckoutController.php`: tạo đơn hàng, tạo chi tiết đơn hàng và trừ tồn kho qua repository.
- `app/Http/Controllers/UserOrderController.php`: lấy đơn hàng của user qua repository.
- `app/Http/Controllers/Admin/ProductController.php`: quản lý sản phẩm qua repository.
- `app/Http/Controllers/Admin/OrderController.php`: quản lý đơn hàng qua repository.
- `app/Http/Controllers/Admin/UserController.php`: quản lý user qua repository.
