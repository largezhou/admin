## 感谢

- [vue-element-admin](https://github.com/PanJiaChen/vue-element-admin)

- [laravel-admin](https://github.com/z-song/laravel-admin)

## 简介

一个带角色权限控制的后台起始项目，前后端分离，`Vue(antd)` + `PHP(Laravel)`

### 功能

- 与 `laravel-admin` 一样的角色权限管理功能

- 文件管理器

- 配置管理

### artisan 命令

- `admin:init` - 初始化系统

- `admin:make-resource` - 生成一个资源所需的所有相关文件

  比如开发一个全新的轮播图功能

  执行 `php artisan admin:make-resource banner`

  可生成一个功能所需的增删改查测的前后端文件 

## DEMO

[http://admin-demo.largezhou.cn/admin](http://admin-demo.largezhou.cn/admin)

## 安装

- `git clone git@github.com:largezhou/admin.git`
- `cd admin`
- `composer install`
- `cp .env.example .env`
- 修改 `.env` 文件中数据库连接配置
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan admin:init`
- `cd resources/admin && yarn && yarn prod`

## nginx 配置

```nginx
# yarn build 之后的路径
location /admin/ {
    try_files $uri $uri/ /admin/index.html;
}

# yarn dev 或者 yarn watch 之后的路径
location /admin-dev/ {
    try_files $uri $uri/ /admin-dev/index.html;
}
# 上面两条配置可选

# Laravel
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## License
[MIT](LICENSE)
