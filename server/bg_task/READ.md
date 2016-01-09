测试服务器：	iZ28q0hy2tvZ
联想WEB服务器	iZ28n5p1by7Z
OPPO,Letv,金立，Meizu的Web服务器	iZ284a1iwu1Z


使用说明：
	1：将代码用git 到目标服务器 ，然后执行 python bin/daemont_task.py  
	2：在目标服务器上 hostname 得到目标机器的机器名字
	3：回到开发机，到开发目录，在conf目录下创建 目标机器名字.ini 文件
	4：git提交然后push
目录：
	1：conf 目标机器的配置
	2：tasks 任务脚本目录
配置：
	[gitpull] #任务脚本名字（git定时更新程序）
	runtime=600  #任务多久执行一次，600 = 10分钟
	args.gitpull=/data/git/bg_task 任务脚本接收到的参数 ，必须是 args开头，之后的是任意字符串，值为task接收的参数 
	#删除昨天的日志文件
	# 执行方式 /bin/RmOneDayFile /data/wwwroot/alios.api.teddymobile.cn/logs/debug /data/wwwroot/aliqd.api.teddymobile.cn/logs/debug
	[RmOneDayFile]
	runtime=86400
	args.alios=/data/wwwroot/alios.api.teddymobile.cn/logs/debug
	args.aliqd=/data/wwwroot/aliqd.api.teddymobile.cn/logs/debug
	[ErrorPHP]
	runtime=600
	args.phpini=/usr/local/php/etc/php.ini
任务：
	支持脚本类型：
		1：可执行文件
		2：python脚本
	Crontab
		1：在crontab目录项创建 目标机器名.conf 的cron配置，内容如：*	1	*	*	*	{$appdir}/test
		2：在crontab下创建 目标机器名的文件件
		3：在crontab/目标机器名/ 下场景运行脚本，修改脚本为可运行 如上 的test脚本
		4：在配置文件中添加 crontab的运行脚本，不需要啊参数
		5：git提交并push，之后目标服务器就会自动添加crontab的任务，只有配置文件有修改之后才会修改目标机器上的计划任务
程序特点：
	随时添加Task，然后修改配置后提交push即可
