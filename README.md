### Cách sử dụng các công cụ phân tích tĩnh

## Build Docker image

```shell
docker build -t php-code-quality https://github.com/TanNhatCMS/php-code-quality.git
```
## Lệnh sử dụng
### 🛠️ Rector – Tự động refactor code PHP theo rule
```shell
docker run -it --rm -v "$PWD":/project -w /project php-code-quality /usr/local/lib/php-code-quality/vendor/bin/rector process --config /usr/local/lib/php-code-quality/rector.php
```
🔹 Chạy Rector để tự động refactor code PHP (ví dụ nâng cấp cú pháp, chuẩn hóa kiểu Laravel, v.v.).
🔹 Dùng cấu hình tùy chỉnh ở /usr/local/lib/php-code-quality/rector.php.

###  Rector Dry-run – Kiểm tra refactor sẽ thay đổi gì mà không áp dụng
```shell
docker run -it --rm -v "$PWD":/project -w /project php-code-quality /usr/local/lib/php-code-quality/vendor/bin/rector process --config /usr/local/lib/php-code-quality/rector.php --dry-run
```
🔹 Giống như lệnh trên nhưng chỉ "xem trước" thay đổi mà không ghi đè file.
🔹 Rất phù hợp để review trước khi commit.

### 🧪 PHPStan – Phân tích tĩnh code, phát hiện lỗi tiềm ẩn
```shell
docker run --rm -t -v "$PWD":/project -w /project php-code-quality php /usr/local/lib/php-code-quality/vendor/bin/phpstan analyse -l 0 --error-format=table
```
🔹 Kiểm tra code bằng phân tích tĩnh ở mức độ nhẹ (level 0).
🔹 Giúp phát hiện lỗi logic, gọi hàm sai, biến chưa định nghĩa mà không cần chạy app.
🔹 Hiển thị kết quả dạng bảng trực quan.

- Lưu ý Windows: Chạy trong Git Bash hoặc WSL (Windows Subsystem for Linux)





----
# php-code-quality
Mục tiêu của tôi là bao gồm nhiều công cụ chất lượng mã PHP trong hình ảnh Docker dễ sử dụng. Các công cụ bao gồm phân tích tĩnh PHP, các dòng báo cáo mã PHP, máy dò lộn xộn, làm nổi bật mùi mùi, phát hiện sao chép/dán và khả năng tương thích ứng dụng từ một phiên bản PHP này sang phiên bản khác cho các nỗ lực hiện đại hóa.
Cụ thể hơn, hình ảnh Docker bao gồm:
- phpstan/phpstan
- squizlabs/php_codesniffer
- phpcompatibility/php-compatibility
- phploc/phploc
- phpmd/phpmd
- pdepend/pdepend
- sebastian/phpcpd
- phpmetrics/phpmetrics
- phpunit/phpunit
- friendsofphp/php-cs-fixer
- rectorphp/rector

Hiện có sẵn thông qua cả kho lưu trữ container Docker và GitHub. (Xem bên dưới)
Repository này dựa trên:
- adamculp/php-code-quality

## Cách sử dụng

Lưu ý: Hình ảnh này không làm gì khi gọi nó mà không có lệnh theo dõi (như được hiển thị bên dưới trong `Một số lệnh ví dụ` cho mỗi công cụ), chẳng hạn như:

```shell
cd </path/to/your/project>
docker run -it --rm -v "$PWD":/project -w /project \
php-code-quality <followup-command-with-arguments>
```

Ngoài ra, lưu ý ví dụ trên là sử dụng kho lưu trữ Docker Hub. Ngoài ra, bạn cũng có thể sử dụng kho lưu trữ gói GitHub bằng cách chi tiêu `ghcr.io/` cho định danh hình ảnh, như sau: (thay thế các trình giữ chỗ trong khung góc bằng các giá trị của bạn.)

```shell
cd </path/to/your/project>
docker run -it --rm -v "$PWD":/project -w /project \
 php-code-quality <followup-command-with-arguments>
```

NGƯỜI DÙNG WINDOWS: Việc sử dụng "$PWD" cho thư mục làm việc hiện tại sẽ không hoạt động như mong đợi, thay vào đó hãy sử dụng "%cd%" hoặc đường dẫn đầy đủ. Ví dụ: "//c/Users/adamculp/project".

Trong ví dụ trên, Docker chạy một terminal tương tác sẽ bị xóa khi hoàn tất, gắn (mount) thư mục hiện tại của máy chủ ($PWD) vào bên trong container, đặt đây làm thư mục làm việc hiện tại, và sau đó tải image `adamculp/php-code-quality` hoặc `ghcr.io/adamculp/php-code-quality` tùy trường hợp.

Tiếp theo, người dùng có thể thêm bất kỳ lệnh nào để thực thi bên trong container. (chẳng hạn như chạy các công cụ do image cung cấp)

Đây là trường hợp sử dụng phổ biến nhất, cho phép người dùng chạy các công cụ trên mọi thứ trong và/hoặc bên dưới thư mục làm việc.

Các lệnh có sẵn do image adamculp/php-code-quality cung cấp:

* sh (hoặc bất kỳ lệnh nào khác) + args
* php + args
* composer + args
* vendor/bin/<lệnh-công-cụ-đã-chọn-bên-dưới> + args

### Một số lệnh ví dụ:

QUAN TRỌNG: Nếu sử dụng các lệnh bên dưới "nguyên trạng", vui lòng tạo một thư mục 'php_code_quality' trong dự án trước. Thư mục này sẽ được các lệnh sử dụng để chứa kết quả của các công cụ khác nhau. Sửa đổi nếu muốn.

QUAN TRỌNG: Nếu bạn gặp sự cố về bộ nhớ, trong đó kết quả đầu ra cho biết tiến trình đã hết bộ nhớ, bạn có thể thay đổi lượng bộ nhớ mà tiến trình PHP sử dụng cho một lệnh nhất định bằng cách thêm cờ -d vào lệnh PHP. Lưu ý rằng ví dụ sau dành cho các trường hợp cực đoan vì image đã đặt giới hạn bộ nhớ là 512M. (không khuyến khích)

```shell
php -d memory_limit=1G
```

#### PHPStan

Xem [Tài liệu PHPStan](https://phpstan.org/user-guide/getting-started) để biết thêm tài liệu về cách sử dụng.

```
docker run -it --rm --name php-code-quality -v "$PWD":/project -w /project \
php-code-quality sh -c 'php /usr/local/lib/php-code-quality/vendor/bin/phpstan \
 analyse -l 0 --error-format=table > ./php_code_quality/phpstan_results.txt .'
```

#### PHP Codesniffer (phpcs)

Xem [PHP_CodeSniffer Wiki](https://github.com/squizlabs/PHP_CodeSniffer/wiki) để biết thêm chi tiết sử dụng công cụ này. (Lưu ý: Lệnh sau hướng dẫn PHP_CodeSniffer sử dụng tiêu chuẩn PSR-12, thay vì tiêu chuẩn PEAR mặc định. Cũng lưu ý dấu . ở cuối.)

```shell
docker run -it --rm -v "$PWD":/project -w /project php-code-quality \
php /usr/local/lib/php-code-quality/vendor/bin/phpcs -sv --standard=PSR12 \
 --extensions=php --ignore=vendor --report-file=./php_code_quality/codesniffer_results.txt .
```

#### Các quy tắc PHPCompatibility áp dụng cho PHP Codesniffer

Xem [PHPCompatibility Readme](https://github.com/PHPCompatibility/PHPCompatibility) và [PHP_CodeSniffer Wiki](https://github.com/squizlabs/PHP_CodeSniffer/wiki) ở trên để biết thêm chi tiết sử dụng công cụ này. PHPCompatibility là một tập hợp các sniff để sử dụng với PHP_CodeSniffer.

Lưu ý: Lệnh sau khác với các lệnh khác, vì nó truyền 2 lệnh PHP thay vì một lệnh duy nhất. Điều này cho phép tải các sniff của PHPCompatibility trước khi sử dụng.

```shell
docker run -it --rm -v "$PWD":/project -w /project php-code-quality sh -c \
'php /usr/local/lib/php-code-quality/vendor/bin/phpcs -sv --config-set installed_paths  /usr/local/lib/php-code-quality/vendor/phpcompatibility/php-compatibility && \
php /usr/local/lib/php-code-quality/vendor/bin/phpcs -sv --standard='PHPCompatibility' --extensions=php --ignore=vendor \
--report-file=./php_code_quality/phpcompatibility_results.txt .'
```

#### PHP Lines of Code (PHPLoc)

Xem [PHPLOC Readme](https://github.com/sebastianbergmann/phploc) để biết thêm chi tiết sử dụng công cụ này.

```
docker run -it --rm -v "$PWD":/project -w /project adamculp/php-code-quality:latest \
php /usr/local/lib/php-code-quality/vendor/bin/phploc  \
--exclude vendor . > ./php_code_quality/phploc.txt
```

#### PHP Mess Detector (phpmd)

QUAN TRỌNG: Hiện đang gặp lỗi khi sử dụng công cụ này. Xem [vấn đề này](https://github.com/phpmd/phpmd/issues/919).

Xem [PHPMD Readme](https://github.com/phpmd/phpmd) để biết thêm chi tiết sử dụng công cụ này.

```
docker run -it --rm -v "$PWD":/project -w /project php-code-quality \
php /usr/local/lib/php-code-quality/vendor/bin/phpmd . xml codesize --exclude 'vendor' \
--reportfile './php_code_quality/phpmd_results.xml'
```

#### PHP Depends (Pdepend)

QUAN TRỌNG: Hiện đang gặp lỗi khi sử dụng công cụ này. Xem [vấn đề này](https://github.com/phpmd/phpmd/issues/919).

Xem [Tài liệu PDepend](https://pdepend.org/) để biết thêm chi tiết sử dụng công cụ này.

Lưu ý: Tôi đã không sử dụng công cụ này một thời gian và nhận thấy nó có thể yêu cầu đăng ký Tidelift để sử dụng.

```
docker run -it --rm -v "$PWD":/project -w /project php-code-quality \
php /usr/local/lib/php-code-quality/vendor/bin/pdepend --ignore='vendor' \
--summary-xml='./php_code_quality/pdepend_output.xml' \
--jdepend-chart='./php_code_quality/pdepend_chart.svg' \
--overview-pyramid='./php_code_quality/pdepend_pyramid.svg' .
```

#### PHP Copy/Paste Detector (phpcpd)

Xem [PHPCPD Readme](https://github.com/sebastianbergmann/phpcpd) để biết thêm chi tiết sử dụng công cụ này.

```shell
docker run -it --rm -v "$PWD":/project -w /project php-code-quality \
php /usr/local/lib/php-code-quality/vendor/bin/phpcpd . \
--exclude 'vendor' > ./php_code_quality/phpcpd_results.txt
```

#### PHPMetrics

Xem http://www.phpmetrics.org/ để biết thêm chi tiết sử dụng công cụ này.

```shell
docker run -it --rm -v "$PWD":/project -w /project php-code-quality:latest \
php /usr/local/lib/php-code-quality/vendor/bin/phpmetrics --excluded-dirs 'vendor' \
--report-html=./php_code_quality/metrics_results .
```

##  Các cách chuẩn bị thay thế

Thay vì cho phép Docker lấy image từ Docker Hub hoặc GitHub Container Repositories, người dùng cũng có thể tự xây dựng Docker image cục bộ bằng cách clone repository của image từ GitHub.

Tại sao? Ví dụ, bạn có thể muốn một phiên bản PHP khác. Hoặc có thể cần một phiên bản cụ thể của bất kỳ công cụ nào.

## Tạo Docker image
Sau khi clone, điều hướng đến vị trí:

```shell
$ git clone https://github.com/TanNhatCMS/php-code-quality.git
$ cd php-code-quality
$ docker build -t php-code-quality .
```

Sửa đổi Dockerfile theo ý muốn, sau đó xây dựng image cục bộ: (đừng bỏ sót dấu chấm ở cuối)

```shell
$ docker build -t php-code-quality .
```

Hoặc người dùng có thể chỉ muốn image nguyên trạng và lưu vào bộ đệm để sử dụng sau:

```shell
$ docker build -t php-code-quality https://github.com/TanNhatCMS/php-code-quality.git
```

## Cách sử dụng các công cụ phân tích tĩnh
### rector
Đây là công cụ giúp sửa đổi các đoạn mã không còn được khuyến khích (deprecated) trong các phiên bản PHP mới.

Sau khi tạo image, hãy chạy lệnh sau. Ở đây, dấu \ được dùng để xuống dòng cho dễ đọc, nhưng bạn có thể chạy lệnh này trên một dòng.

```
$ cd <targer_repo_root>
$ docker run --rm -t \
-v `pwd`:`pwd` -w `pwd` php-code-quality \
/usr/local/lib/php-code-quality/vendor/bin/rector process \
--config /usr/local/lib/php-code-quality/rector.php <target_file_dir>
```
- `<targer_repo_root>`:  Di chuyển đến thư mục gốc của repository mà bạn muốn chạy công cụ, sau đó thực thi rector.
-  `-t`:  Kích hoạt TTY. Nếu không kích hoạt, kết quả thực thi của rector sẽ không có màu. Lệnh `-it` cũng hoạt động, nhưng sẽ gây lỗi khi chạy dưới dạng yarn scripts.
-  `-v`, `-w`:  Gắn (mount) thư mục hiện tại nơi lệnh được thực thi. Do `pwd`  được dùng để chỉ định thư mục, đường dẫn trong Docker container sẽ khớp với đường dẫn trên máy host, cho phép bạn truyền trực tiếp đường dẫn của máy host cho công cụ bên trong Docker.
-  `/usr/local/lib/php-code-quality/vendor/bin/rector process`: Lệnh thực thi rector.
- `--config /usr/local/lib/php-code-quality/rector.php`: Chỉ định file cấu hình của rector. Bạn cũng có thể tạo file cấu hình trong repository của mình, nhưng ở đây chúng ta chỉ định file cấu hình có sẵn trong Docker image.
- `<target_file_dir>`: Thay thế bằng thư mục (hoặc tên một file đơn lẻ) bạn muốn phân tích. Có thể chỉ định bằng đường dẫn tuyệt đối trên máy host (máy tính của bạn) hoặc đường dẫn tương đối từ thư mục hiện tại.

### phpstan
Công cụ thực hiện phân tích tĩnh mã PHP.

Tạo file phpstan.neon trong thư mục `<target_file_dir>` (※ Dùng để load các file PHP phụ thuộc, tuy nhiên vẫn có thể chạy mà không cần file này)

```
parameters:
    bootstrapFiles:
     - vendor/autoload.php
```

2. Sau khi tạo image, hãy chạy lệnh sau. Ở đây, dấu \ được dùng để xuống dòng cho dễ đọc, nhưng bạn có thể chạy lệnh này trên một dòng.

```
docker run --rm -t -v `pwd`:`pwd` -w `pwd` \
php-code-quality:latest /usr/local/lib/php-code-quality/vendor/bin/phpstan \
analyse --level 5 --error-format=table <target_file_dir>
```

- `<target_file_dir>`: Thay thế bằng thư mục (hoặc tên một file đơn lẻ) bạn muốn phân tích.
- `php /usr/local/lib/php-code-quality/vendor/bin/phpstan`:Lệnh thực thi phpstan.
- `analyze`:Lệnh để thực hiện phân tích tĩnh bằng phpstan.
- `--level <num>`:Chỉ định mức độ nghiêm ngặt của phân tích tĩnh, với giá trị tối đa là 10. Xem chi tiết[tại đây](https://phpstan.org/user-guide/rule-levels)
- `--error-format=X`:Chỉ định định dạng xuất lỗi. `table` là chế độ hiển thị lỗi được phân tách theo từng file. Xem chi tiết[tại đây](https://phpstan.org/user-guide/output-format)


### Các công cụ phân tích tĩnh khác ngoài những công cụ trên

Vui lòng tham khảo repository gốc đã clone.

https://github.com/adamculp/php-code-quality