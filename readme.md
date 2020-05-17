## 感谢

感谢以下两个项目供我参考

- [vue-element-admin](https://github.com/PanJiaChen/vue-element-admin)
- [laravel-admin](https://github.com/z-song/laravel-admin)

## 简介

一个带角色权限控制的后台起始项目，前后端分离，`Vue(antd)` + `PHP(Laravel)`

角色权限部分用的 `laravel-admin` 中的方案

还附赠了两个功能：

- 文件管理器，可以统一管理后台的文件上传和选择
- 配置管理

## DEMO

[http://admin-demo.largezhou.cn/admin/vue-routers](http://admin-demo.largezhou.cn/admin/vue-routers)

账号：admin

密码：000000

搞坏了可点【重置】

## 安装

- `git clone git@github.com:largezhou/admin.git`
- `cd admin`
- `composer install`
- `cp .env.example .env`
- 修改 `.env` 文件中数据库连接配置
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan admin:init`
- `cd resources/admin && yarn && yarn build`

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
