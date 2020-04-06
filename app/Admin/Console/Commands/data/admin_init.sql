/*
 Navicat Premium Data Transfer

 Source Server         : wsl
 Source Server Type    : MySQL
 Source Server Version : 50728
 Source Host           : 127.0.0.1:3306
 Source Schema         : admin

 Target Server Type    : MySQL
 Target Server Version : 50728
 File Encoding         : 65001

 Date: 06/04/2020 23:27:55
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `http_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES (1, '所有权限', 'pass-all', NULL, '*', '2020-04-06 23:15:45', '2020-04-06 23:15:45');
INSERT INTO `admin_permissions` VALUES (2, '角色权限管理', 'manage-auth', NULL, '/admin-users*\n/admin-roles*\n/admin-permissions*\n/vue-routers*', '2020-04-06 23:18:08', '2020-04-06 23:22:19');
INSERT INTO `admin_permissions` VALUES (3, '文件管理', 'manage-media', NULL, '/system-media*', '2020-04-06 23:18:32', '2020-04-06 23:19:25');
INSERT INTO `admin_permissions` VALUES (4, '配置管理', 'manage-config', NULL, '/configs*\n/config-categories*', '2020-04-06 23:19:19', '2020-04-06 23:19:19');
INSERT INTO `admin_permissions` VALUES (5, '系统设置', 'config-system-basic', NULL, '/configs/system_basic*', '2020-04-06 23:20:46', '2020-04-06 23:20:46');

-- ----------------------------
-- Table structure for admin_role_permission
-- ----------------------------
DROP TABLE IF EXISTS `admin_role_permission`;
CREATE TABLE `admin_role_permission`  (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  INDEX `admin_role_permission_role_id_index`(`role_id`) USING BTREE,
  INDEX `admin_role_permission_permission_id_index`(`permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_role_permission
-- ----------------------------
INSERT INTO `admin_role_permission` VALUES (1, 1);
INSERT INTO `admin_role_permission` VALUES (2, 5);
INSERT INTO `admin_role_permission` VALUES (2, 3);

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
INSERT INTO `admin_roles` VALUES (1, '超级管理员', 'administrator', '2020-04-06 23:15:45', '2020-04-06 23:15:45');
INSERT INTO `admin_roles` VALUES (2, '演示', 'demo', '2020-04-06 23:21:16', '2020-04-06 23:21:16');

-- ----------------------------
-- Table structure for admin_user_permission
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_permission`;
CREATE TABLE `admin_user_permission`  (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  INDEX `admin_user_permission_user_id_index`(`user_id`) USING BTREE,
  INDEX `admin_user_permission_permission_id_index`(`permission_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_user_permission
-- ----------------------------

-- ----------------------------
-- Table structure for admin_user_role
-- ----------------------------
DROP TABLE IF EXISTS `admin_user_role`;
CREATE TABLE `admin_user_role`  (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  INDEX `admin_user_role_user_id_index`(`user_id`) USING BTREE,
  INDEX `admin_user_role_role_id_index`(`role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_user_role
-- ----------------------------
INSERT INTO `admin_user_role` VALUES (1, 1);
INSERT INTO `admin_user_role` VALUES (2, 2);

-- ----------------------------
-- Table structure for admin_users
-- ----------------------------
DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `admin_users_username_index`(`username`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_users
-- ----------------------------
INSERT INTO `admin_users` VALUES (1, 'admin', '$2y$10$HlmwOTWAPiFuenkFPdTStO6M8UgKc5.vi0YxYiSwY0eyjuRU3hoIS', '管理员', NULL, '2020-04-06 23:15:45', '2020-04-06 23:15:45');
INSERT INTO `admin_users` VALUES (2, 'demo', '$2y$10$VAU0vVK0u92I5r6VAfhn/uQtgDMDkJol0F.TT7mIMyh3JxbVciQ6y', '没啥权限', NULL, '2020-04-06 23:17:21', '2020-04-06 23:17:21');

-- ----------------------------
-- Table structure for config_categories
-- ----------------------------
DROP TABLE IF EXISTS `config_categories`;
CREATE TABLE `config_categories`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `config_categories_slug_index`(`slug`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of config_categories
-- ----------------------------
INSERT INTO `config_categories` VALUES (1, '系统设置', 'system_basic', '2020-04-06 23:15:45', '2020-04-06 23:15:45');

-- ----------------------------
-- Table structure for configs
-- ----------------------------
DROP TABLE IF EXISTS `configs`;
CREATE TABLE `configs`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'input',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `options` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '填写配置时的选项，比如单选、多选下拉的选项',
  `value` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `validation_rules` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '验证规则',
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `configs_category_id_index`(`category_id`) USING BTREE,
  INDEX `configs_slug_index`(`slug`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of configs
-- ----------------------------
INSERT INTO `configs` VALUES (1, 1, 'input', '系统名称', 'app_name', NULL, NULL, '\"\\u540e\\u53f0\"', 'required|string|max:20', '2020-04-06 23:15:45', '2020-04-06 23:24:11');
INSERT INTO `configs` VALUES (2, 1, 'file', '系统 LOGO', 'app_logo', NULL, '{\"max\":1,\"ext\":\"jpg,png,jpeg\"}', NULL, 'nullable|string', '2020-04-06 23:15:45', '2020-04-06 23:15:45');
INSERT INTO `configs` VALUES (3, 1, 'file', '登录背景图', 'login_background', NULL, '{\"max\":1,\"ext\":\"jpg,png,jpeg\"}', NULL, 'nullable|string', '2020-04-06 23:15:45', '2020-04-06 23:15:45');
INSERT INTO `configs` VALUES (4, 1, 'other', '首页路由', 'home_route', NULL, NULL, '\"1\"', 'required|exists:vue_routers,id', '2020-04-06 23:15:45', '2020-04-06 23:15:45');
INSERT INTO `configs` VALUES (5, 1, 'input', 'CDN 域名', 'cdn_domain', NULL, NULL, '\"\\/\"', 'required|string', '2020-04-06 23:15:45', '2020-04-06 23:15:45');

-- ----------------------------
-- Table structure for vue_router_role
-- ----------------------------
DROP TABLE IF EXISTS `vue_router_role`;
CREATE TABLE `vue_router_role`  (
  `vue_router_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  INDEX `vue_router_role_vue_router_id_index`(`vue_router_id`) USING BTREE,
  INDEX `vue_router_role_role_id_index`(`role_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vue_router_role
-- ----------------------------

-- ----------------------------
-- Table structure for vue_routers
-- ----------------------------
DROP TABLE IF EXISTS `vue_routers`;
CREATE TABLE `vue_routers`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `icon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `menu` tinyint(4) NOT NULL DEFAULT 0,
  `cache` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  `permission` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 106 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of vue_routers
-- ----------------------------
INSERT INTO `vue_routers` VALUES (1, 0, '首页', 'index', 1, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (2, 105, '路由配置', NULL, 3, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (3, 2, '所有路由', 'vue-routers', 4, NULL, 1, 1, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (4, 2, '添加路由', 'vue-routers/create', 5, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (5, 2, '编辑路由', 'vue-routers/:id(\\d+)/edit', 6, NULL, 0, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (6, 0, '管理员管理', NULL, 7, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', 'manage-auth');
INSERT INTO `vue_routers` VALUES (7, 6, '管理员列表', 'admin-users', 8, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (8, 6, '添加管理员', 'admin-users/create', 9, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (9, 6, '编辑管理员', 'admin-users/:id(\\d+)/edit', 10, NULL, 0, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (10, 0, '角色管理', NULL, 11, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', 'manage-auth');
INSERT INTO `vue_routers` VALUES (11, 10, '角色列表', 'admin-roles', 12, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (12, 10, '添加角色', 'admin-roles/create', 13, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (13, 10, '编辑角色', 'admin-roles/:id(\\d+)/edit', 14, NULL, 0, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (14, 0, '权限管理', NULL, 15, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', 'manage-auth');
INSERT INTO `vue_routers` VALUES (15, 14, '权限列表', 'admin-permissions', 16, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', NULL);
INSERT INTO `vue_routers` VALUES (16, 14, '添加权限', 'admin-permissions/create', 17, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (17, 14, '编辑权限', 'admin-permissions/:id(\\d+)/edit', 18, NULL, 0, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (18, 0, '文件管理', 'system-media', 19, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (19, 0, '配置管理', NULL, 20, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', 'manage-config');
INSERT INTO `vue_routers` VALUES (20, 19, '配置分类', 'config-categories', 21, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (21, 19, '所有配置', 'configs', 22, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (22, 19, '添加配置', 'configs/create', 23, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (23, 19, '编辑配置', 'configs/:id(\\d+)/edit', 24, NULL, 0, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (24, 0, '系统设置', '/configs/system_basic', 25, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (99, 0, '权限测试', '/permission-test', 26, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (100, 0, '菜单匹配测试', NULL, 27, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', 'manage-auth');
INSERT INTO `vue_routers` VALUES (101, 0, '链接', 'https://www.baidu.com', 31, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (102, 100, '编辑路由 3', '/vue-routers/3/edit', 28, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (103, 100, '带 query a=1', '/admin-users?a=1', 29, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (104, 100, '带 query b=1&a=1', '/admin-users?b=1&a=1', 30, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:54', NULL);
INSERT INTO `vue_routers` VALUES (105, 0, '嵌套1', NULL, 2, NULL, 1, 0, '2020-04-06 23:15:45', '2020-04-06 23:24:53', 'manage-auth');

SET FOREIGN_KEY_CHECKS = 1;
