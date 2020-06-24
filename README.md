# PHP-encryption
PHP服务器加密，解决API cookie和session不能验证的问题
原理：
1 服务器生成一个加密字符串返回客户端
2 客户端发送加密字符串验证是否服务器发送的数据
