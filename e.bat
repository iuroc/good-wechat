@REM 一键提交 Git 脚本
git add .
set /p message=Please Input Message: 
git commit -m "%message%"
git push