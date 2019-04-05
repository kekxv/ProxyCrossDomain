# ProxyCrossDomain

PHP 跨域代理

## 用法

`GET` 请求以及 `PUSH` 请求对应代理的请求

> 参数说明
> 1. `url` 请求地址
> 1. `data` 请求数据
> 1. `isJson` 是否是Json格式数据，仅 POST 有效
> 1. `refererUrl` 跳转地址
> 

## 返回

`JSON` 格式

> 返回说明
> 1. `Code` 结果代码
> 1. `Message` 结果信息
> 1. `Result:` 结果内容