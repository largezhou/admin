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

还附赠了一个文件管理，可以统一管理后台的文件上传和选择，，不过还没试过接入第三方云存储。

## DEMO

[http://admin-demo.largezhou.cn/admin/vue-routers](http://admin-demo.largezhou.cn/admin/vue-routers)

账号：admin

密码：000000

搞坏了可点【重置】

## 安装

- `Laravel` 安装流程先走一套
- `php artisan jwt:secret`
- `php artisan admin:init` 初始化数据库的数据（超管，角色，权限和路由）
- `yarn && yarn build` 推荐使用 `yarn` 哦
- 其他环境配置，看看 `.env.example` 中的注释

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

# Laravel
location / {
    try_files $uri $uri/ /index.php$is_args$args;
}
```

## License
[MIT](LICENSE)
