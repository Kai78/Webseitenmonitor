Dim WinScriptHost
Set WinScriptHost = CreateObject("WScript.Shell")
WinScriptHost.Run Chr(34) & "C:\xampp\htdocs\Webseitenmonitor\check.bat" & Chr(34), 0
Set WinScriptHost = Nothing