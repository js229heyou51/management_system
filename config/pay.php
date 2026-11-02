<?php
return [
    'alipay' => [
        'default' => [
            // 「必填」支付宝分配的 app_id
            'app_id' => '2016082000295641',
            // 「必填」应用私钥 字符串或路径
            // 在 https://open.alipay.com/develop/manage 《应用详情->开发设置->接口加签方式》中设置
            'app_secret_cert' => 'MIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCDRjOg5DnX+8L+rB8d2MbrQ30Z7JPM4hiDhawHSwQCQ7RlmQNpl6b/N6IrPLcPFC1uii179U5Il5xTZynfjkUyJjnHusqnmHskftLJDKkmGbSUFMAlOv+NlpUWMJ2A+VUopl+9FLyqcV+XgbaWizxU3LsTtt64v89iZ2iC16H6/6a3YcP+hDZUjiNGQx9cuwi9eJyykvcwhDkFPxeBxHbfwppsul+DYUyTCcl0Ltbga/mUechk5BksW6yPPwprYHQBXyM16Jc3q5HbNxh3660FyvUBFLuVWIBs6RtR2gZCa6b8rOtCkPQKhUKvzRMlgheOowXsWdk99GjxGQDK5W4XAgMBAAECggEAYPKnjlr+nRPBnnNfR5ugzH67FToyrU0M7ZT6xygPfdyijaXDb2ggXLupeGUOjIRKSSijDrjLZ7EQMkguFHvtfmvcoDTDFaL2zq0a3oALK6gwRGxOuzAnK1naINkmeOmqiqrUab+21emEv098mRGbLNEXGCgltCtz7SiRdo/pgIPZ1wHj4MH0b0K2bFG3xwr51EyaLXKYH4j6w9YAXXsTdvzcJ+eRE0Yq4uGPfkziqg8d0xXSEt90HmCGHKo4O2eh1w1IlBcHfK0F6vkeUAtrtAV01MU2bNoRU147vKFxjDOVBlY1nIZY/drsbiPMuAfSsodL0hJxGSYivbKTX4CWgQKBgQDd0MkF5AIPPdFC+fhWdNclePRw4gUkBwPTIUljMP4o+MhJNrHp0sEy0sr1mzYsOT4J20hsbw/qTnMKGdgy784bySf6/CC7lv2hHp0wyS3Es0DRJuN+aTyyONOKGvQqd8gvuQtuYJy+hkIoHygjvC3TKndX1v66f9vCr/7TS0QPywKBgQCXgVHERHP+CarSAEDG6bzI878/5yqyJVlUeVMG5OXdlwCl0GAAl4mDvfqweUawSVFE7qiSqy3Eaok8KHkYcoRlQmAefHg/C8t2PNFfNrANDdDB99f7UhqhXTdBA6DPyW02eKIaBcXjZ7jEXZzA41a/zxZydKgHvz4pUq1BdbU5ZQKBgHyqGCDgaavpQVAUL1df6X8dALzkuqDp9GNXxOgjo+ShFefX/pv8oCqRQBJTflnSfiSKAqU2skosdwlJRzIxhrQlFPxBcaAcl0VTcGL33mo7mIU0Bw2H1d4QhAuNZIbttSvlIyCQ2edWi54DDMswusyAhHxwz88/huJfiad1GLaLAoGASIweMVNuD5lleMWyPw2x3rAJRnpVUZTc37xw6340LBWgs8XCEsZ9jN4t6s9H8CZLiiyWABWEBufU6z+eLPy5NRvBlxeXJOlq9iVNRMCVMMsKybb6b1fzdI2EZdds69LSPyEozjkxdyE1sqH468xwv8xUPV5rD7qd83+pgwzwSJkCgYBrRV0OZmicfVJ7RqbWyneBG03r7ziA0WTcLdRWDnOujQ9orhrkm+EY2evhLEkkF6TOYv4QFBGSHfGJ0SwD7ghbCQC/8oBvNvuQiPWI8B+00LwyxXNrkFOxy7UfIUdUmLoLc1s/VdBHku+JEd0YmEY+p4sjmcRnlu4AlzLxkWUTTg==',
            // 「必填」应用公钥证书 路径
            // 设置应用私钥后，即可下载得到以下3个证书
            'app_public_cert_path' => '/Users/yansongda/pay/cert/appCertPublicKey_2016082000295641.crt',
            // 「必填」支付宝公钥证书 路径
            'alipay_public_cert_path' => '/Users/yansongda/pay/cert/alipayCertPublicKey_RSA2.crt',
            // 「必填」支付宝根证书 路径
            'alipay_root_cert_path' => '/Users/yansongda/pay/cert/alipayRootCert.crt',
            'return_url' => 'https://yansongda.cn/alipay/return',
            'notify_url' => 'https://yansongda.cn/alipay/notify',
            // 「选填」第三方应用授权token
            'app_auth_token' => '',
            // 「选填」服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => '',
            // 「选填」AES 密钥，用于解密响应内容（如会员手机号等敏感信息接口）
            'aes_key' => '',
            // 「选填」默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => '',
        ]
    ],
    'wechat' => [
        'default' => [
            // 「必填」商户号，服务商模式下为服务商商户号
            // 可在 https://pay.weixin.qq.com/ 账户中心->商户信息 查看
            'mch_id' => '',
            // 「选填」v2商户私钥
            'mch_secret_key_v2' => '',
            // 「必填」v3 商户秘钥
            // 即 API v3 密钥(32字节，形如md5值)，可在 账户中心->API安全 中设置
            'mch_secret_key' => '',
            // 「必填」商户私钥 字符串或路径
            // 即 API证书 PRIVATE KEY，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_key.pem
            'mch_secret_cert' => '',
            // 「必填」商户公钥证书路径
            // 即 API证书 CERTIFICATE，可在 账户中心->API安全->申请API证书 里获得
            // 文件名形如：apiclient_cert.pem
            'mch_public_cert_path' => '',
            // 「必填」微信回调url
            // 不能有参数，如?号，空格等，否则会无法正确回调
            'notify_url' => 'https://yansongda.cn/wechat/notify',
            // 「选填」公众号 的 app_id
            // 可在 mp.weixin.qq.com 设置与开发->基本配置->开发者ID(AppID) 查看
            'mp_app_id' => '2016082000291234',
            // 「选填」小程序 的 app_id
            'mini_app_id' => '',
            // 「选填」app 的 app_id
            'app_id' => '',
            // 「选填」服务商模式下，子公众号 的 app_id
            'sub_mp_app_id' => '',
            // 「选填」服务商模式下，子 app 的 app_id
            'sub_app_id' => '',
            // 「选填」服务商模式下，子小程序 的 app_id
            'sub_mini_app_id' => '',
            // 「选填」服务商模式下，子商户id
            'sub_mch_id' => '',
            // 「选填」虚拟支付配置（小程序虚拟支付时必填）
            'virtual_pay' => [
                // 「必填」商户号（offer_id）
                'offer_id' => '1234567890',
                // 「必填」签名密钥（客户端签名、服务端 API 签名）
                'app_key' => 'your_app_key',
                // 「选填」沙箱环境签名密钥
                'sandbox_app_key' => 'your_sandbox_app_key',
                // 「选填」服务端 API 自动获取 access_token 用
                // 配置后启用自动获取（stable_token 接口）；仅用客户端签名可不配置
                'app_secret' => 'your_app_secret',
                // 「必填」回调验签 Token
                'callback_token' => 'your_callback_token',
                // 「必填」回调解密密钥（43 字符）
                'encoding_aes_key' => 'your_encoding_aes_key',
            ],
            // 「选填」（适用于 2024-11 及之前开通微信支付的老商户）微信支付平台证书序列号及证书路径，强烈建议 php-fpm 模式下配置此参数
            // 「必填」微信支付公钥ID及证书路径，key 填写形如 PUB_KEY_ID_0000000000000024101100397200000006 的公钥id，见 https://pay.weixin.qq.com/doc/v3/merchant/4013053249
            'wechat_public_cert_path' => [
                '45F59D4DABF31918AFCEC556D5D2C6E376675D57' => __DIR__.'/Cert/wechatPublicKey.crt',
                'PUB_KEY_ID_0000000000000024101100397200000006' => __DIR__.'/Cert/publickey.pem',
            ],
            // 「选填」默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
            'mode' => '',
        ]
    ],
    'unipay' => [
        'default' => [
            // 「必填」商户号
            'mch_id' => '777290058167151',
            // 「选填」商户密钥：为银联条码支付综合前置平台配置：https://up.95516.com/open/openapi?code=unionpay
            'mch_secret_key' => '979da4cfccbae7923641daa5dd7047c2',
            // 「必填」商户公私钥
            'mch_cert_path' => __DIR__.'/Cert/unipayAppCert.pfx',
            // 「必填」商户公私钥密码
            'mch_cert_password' => '000000',
            // 「必填」银联公钥证书路径
            'unipay_public_cert_path' => __DIR__.'/Cert/unipayCertPublicKey.cer',
            // 「必填」
            'return_url' => 'https://yansongda.cn/unipay/return',
            // 「必填」
            'notify_url' => 'https://yansongda.cn/unipay/notify',
            'mode' => '',
        ],
    ],
    'douyin' => [
        'default' => [
            // 「必填」小程序 app_id（即 client_key）
            // 抖音开放平台 --> 应用详情 --> 基础信息
            'app_id' => 'tt226e54d3bd581bf801',
            // 「必填」应用密钥，用于获取 client_token
            // 抖音开放平台 --> 应用详情 --> 基础信息
            'app_secret' => 'your-app-secret',
            // 「必填」应用私钥，用于下单请求加签
            // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> 小程序支付密钥，自行生成后上传公钥
            'app_private_key' => "-----BEGIN RSA PRIVATE KEY-----\n...",
            // 「必填」抖音平台公钥，用于支付回调验签
            // 抖音开放平台 --> 应用详情 --> 支付信息 --> 支付设置 --> 抖音平台公钥
            'douyin_public_key' => "-----BEGIN PUBLIC KEY-----\n...",
            // 「选填」抖音支付/退款回调地址
            'notify_url' => 'https://yansongda.cn/douyin/notify',
        ],
    ],
    'paypal' => [
        'default' => [
            // 「必填」PayPal Client ID
            // 在 https://developer.paypal.com/dashboard/applications 中创建应用后获取
            'client_id' => '',
            // 「必填」PayPal App Secret
            // 在 https://developer.paypal.com/dashboard/applications 中创建应用后获取
            'app_secret' => '',
            // 「必填」PayPal Webhook ID
            // 在 https://developer.paypal.com/dashboard/applications 中配置 Webhook 后获取，用于回调签名验证
            'webhook_id' => '',
            // 「选填」支付完成后 PayPal 跳转的回调地址
            'return_url' => 'https://yansongda.cn/paypal/return',
            // 「选填」用户取消支付后 PayPal 跳转的取消地址
            'cancel_url' => 'https://yansongda.cn/paypal/cancel',
            // 「选填」异步通知地址（如有需要请自行在 PayPal 后台配置 Webhook）
            'notify_url' => 'https://yansongda.cn/paypal/notify',
            // 「选填」默认为正常模式。可选为： MODE_NORMAL:正式环境, MODE_SANDBOX:沙箱环境
            'mode' => '',
        ],
    ],
    'stripe' => [
        'default' => [
            // 「必填」Stripe Secret Key
            // 在 https://dashboard.stripe.com/apikeys 中获取
            'secret_key' => '',
            // 「必填」Stripe Webhook Secret
            // 在 https://dashboard.stripe.com/webhooks 中配置 Webhook 后获取，用于回调签名验证
            'webhook_secret' => '',
            // 「选填」支付完成后 Stripe 跳转的成功回调地址（Checkout Session 模式使用）
            'success_url' => 'https://yansongda.cn/stripe/success',
            // 「选填」用户取消支付后 Stripe 跳转的取消地址（Checkout Session 模式使用）
            'cancel_url' => 'https://yansongda.cn/stripe/cancel',
            // 「选填」默认为正常模式。可选为： MODE_NORMAL:正式环境, MODE_SANDBOX:沙箱环境
            'mode' => '',
        ],
    ],
    'jsb' => [
        'default' => [
            // 服务代码
            'svr_code' => '',
            // 「必填」合作商ID
            'partner_id' => '',
            // 「必填」公私钥对编号
            'public_key_code' => '00',
            // 「必填」商户私钥(加密签名)
            'mch_secret_cert_path' => '',
            // 「必填」商户公钥证书路径(提供江苏银行进行验证签名用)
            'mch_public_cert_path' => '',
            // 「必填」江苏银行的公钥(用于解密江苏银行返回的数据)
            'jsb_public_cert_path' => '',
            // 支付通知地址
            'notify_url' => '',
            // 「选填」默认为正常模式。可选为： MODE_NORMAL:正式环境, MODE_SANDBOX:测试环境
            'mode' => '',
        ],
    ],
    'logger' => [
        'enable' => false,
        'file' => './logs/pay.log',
        'level' => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
];