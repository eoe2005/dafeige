#-*- coding:utf8 -*-
import os,sys,sqlite3,time,ConfigParser
class Tasks:
    def __init__(self):
        self.__DIR = os.path.dirname(sys.path[0]) + os.sep
        self.__HOSTNAME = os.uname()[1]
        self.__CONFNAME = self.__DIR + 'conf' + os.sep + self.__HOSTNAME + ".ini"
        self.__APPDIR = self.__DIR + 'tasks' + os.sep
        self.__DBNAME = self.__DIR + 'conf' + os.sep + self.__HOSTNAME + ".db"
        if os.fork():
            sys.exit()
        else:
            self.run() #这样子程序只要一起的就会退出父进程，实现daemon的模式
        #self.run()
    # 解析配置文件的内容和信息
    def __parseIni(self):
        if os.path.exists(self.__CONFNAME): #解析配置文件
            conf = ConfigParser.ConfigParser()
            conf.read(self.__CONFNAME)
            return conf
        return None
    def run(self):
        while True:#主程序进行
            try:
                conf = self.__parseIni()
                if conf is not None:
                    db = DB(self.__DBNAME)
                    for task in conf.sections():
                        runfile = self.__APPDIR + task
                        if os.path.exists(runfile):
                            subfix = task[task.rfind("."):]
                            cmd = None
                            args = []
                            if subfix == '.py':
                                cmd = sys.executable
                                args.append(runfile)

                            else:
                                cmd = runfile
                            for item in conf.items(task):
                                if item[0].startswith('args'):
                                    args.append(item[1])
                            rumtime = conf.getint(task,'runtime')
                            lasttime = db.fetchTaskLastTime(task)
                            if time.time() - rumtime >= lasttime:
                                pid = os.fork()
                                if pid == 0:
                                    args.insert(0,cmd)
                                    os.execv(cmd,args)
                                #os.spawnl(os.P_NOWAIT,cmd,args)
                                else:
                                    os.waitpid(pid,0)
                                db.setTaskTime(task)
                            else:
                                pass #print '不用执行'
                        #
                    #app = 'test'
                    #print db.fetchTaskLastTime(app)
                    #db.setTaskTime(app)
                    del db
            except Exception,e:
                print "Error" + e
            finally:
                time.sleep(3)
# 针对数据库的操作
class DB:
    def __init__(self,filename):
        if os.path.exists(filename):
            self.db = sqlite3.connect(filename)
            self.cu = self.db.cursor()
        else:#保存的数据不存在
            self.db = sqlite3.connect(filename)
            self.cu = self.db.cursor()
            self.cu.execute("CREATE TABLE tasks(taskname VARCHAR(255) PRIMARY KEY,lasttime INTEGER)")
            self.db.commit()
    # 查询应用的最后执行时间
    def fetchTaskLastTime(self,task):
        self.cu.execute("SELECT lasttime FROM tasks WHERE taskname=:task",{'task' : task})
        row = self.cu.fetchone()
        if row:
            return row[0]
        else:
            self.cu.execute("INSERT INTO tasks(taskname,lasttime) VALUES(:task,:lasttime)",{'task' : task,'lasttime' : 0})
            self.db.commit()
            return 0
    def setTaskTime(self,task):
        self.cu.execute("UPDATE tasks SET lasttime=:lasttime WHERE taskname=:task",{'task' : task,'lasttime' : time.time()})
        self.db.commit()
    def __del__(self):
        self.cu.close()
        self.db.close()
if __name__ == '__main__':
    Tasks()
