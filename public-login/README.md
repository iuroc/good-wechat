# 统一扫码登录系统

> 统一登录页面，微信扫码登录，实质是辅助验证微信用户，为网站轻松接入用户系统

## 数据库设计

### 应用表

表名：`gw_login_app`

| 字段         | 描述     |
| ------------ | -------- |
| app_id       | 应用ID   |
| callback_url | 回调 URL |
| create_time  | 创建时间 |

### Token表

表名：`gw_user_token`

| 字段        | 描述       |
| ----------- | ---------- |
| user_id     | 用户 ID    |
| token       | Token 令牌 |
| app_id      | 应用 ID    |
| create_time | 创建时间   |
| expiry_date | 过期时间   |

### 验证码表

表名：`gw_ver_code`

| 字段        | 描述     |
| ----------- | -------- |
| ver_code    | 验证码   |
| user_id     | 用户 ID  |
| expiry_date | 过期时间 |