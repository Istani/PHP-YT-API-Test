var self = module.exports = {
  init: function (MySQL) {
    mysql=MySQL;
  },
  execute: function (message_row, SendFunc, NewMessageFunc) {
    self.reload_commands();
    var returnmsg="";
    returnmsg+="**Commands:**\r\n";
    for (cmd in commands) {
      returnmsg+="!" + cmd.toString()+"\r\n";
      if (typeof cmd.description == 'function') {
        returnmsg+=cmd.description()+"\r\n";
      }
    }
    SendFunc(returnmsg);
  },
  description: function() {
    var returnvalue="";
    returnvalue+="Listet alle Befehle auf!";
    return returnvalue;
  },
  reload_commands: function () {
    var fs = require('fs');
    var files = fs.readdirSync("./command_scripts/");
    commands=null;
    commands={};
    for (var i = 0; i<files.length;i++) {
      var filename=files[i];
      var cmd_name=filename.split(".")[0];
      commands[cmd_name]=require("../command_scripts/"+filename);
      if (typeof commands[cmd_name].init == 'function') {
        commands[cmd_name].init(mysql);
      }
    }
    return commands;
  },
  use: function (command, message_row, SendFunc, NewMessageFunc) {
    if ((command in commands)) {
      if (typeof commands[command].execute == 'function') {
        // TODO: Vielleicht hier Permissions Abfragen?!?
        time = Date.now();
        commands[command].execute(message_row, SendFunc, NewMessageFunc);
      }
    }
  },
  is_command: function (command) {
    var returnvalue=false;
    if ((command in commands)) {
      if (typeof commands[command].execute == 'function') {
        returnvalue=true;
      }
    }
    return returnvalue;
  }
};
var mysql=null;
var commands={};
