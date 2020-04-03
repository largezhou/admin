## 感（bao）谢（ming）

非非非常感谢以下两个项目供我参（chao）考（xi）！！！

- [vue-element-admin](https://github.com/PanJiaChen/vue-element-admin)
- [laravel-admin](https://github.com/z-song/laravel-admin)

项目里部分代码是直接照搬上面两个项目的，有的会有少许调整。
有的文件里写了，有的没写，反正如果看到代码很眼熟，没错，那就是 “抄” 来的。

还有极少的其他开源项目的代码，，，就不一一贴出来了，，，

## 简介

一个带角色权限控制的后台起始项目，前后端分离，`Vue` + `Laravel`。

角色权限部分用的 `laravel-admin` 中的方案和代码，可以说是很成熟的方案了吧，，，

还附赠了两个功能：

- 文件管理器，可以统一管理后台的文件上传和选择，，经过又拍云测试，可以无缝切换本地存储和云存储
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
    try_files $uri $uri/ /index.php$is_args$args;
}
```

## License
[MIT](LICENSE)
