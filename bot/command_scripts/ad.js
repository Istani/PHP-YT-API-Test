var self = module.exports = {
  init: function (MySQL) {
    mysql=MySQL;
  },
  check_permission: function (message_row, SendFunc, NewMessageFunc) {
    var permissions=false;
    
    if (message_row.user=="-1") {
      permissions=true;
    }
    
    permissions=true; // Fake Recht!
    
    if (permissions==false) {
      SendFunc(message_row.user+ " du hast keine Rechte den Befehl auszuführen!\r\n" + message_row.message);
    } else {
      self.execute(message_row, SendFunc, NewMessageFunc);
    }
  },
  execute: function (message_row, SendFunc, NewMessageFunc) {
    var SQL = "SELECT * FROM user_ads ORDER BY count LIMIT 1";
    mysql.query(SQL, function (err, rows) {
      if (err != null) {
        console.log(SQL);
        console.log(err);
        return;
      }
      for (var i = 0; i<rows.length;i++) {
        var SQL2 = "SELECT * FROM user_ads WHERE count='"++"' ORDER BY Rand() LIMIT 1";
        mysql.query(SQL2, function (err2, rows2) {
          if (err != null) {
            console.log(SQL2);
            console.log(err);
            return;
          }
          
        });
      }
      
      var ReturnString="(AD)\r\n";
      for (var i = 0; i<rows.length;i++) {
        var RowString="";
        RowString=rows[i].title;
        RowString+=": ";
        RowString+=rows[i].link;
        RowString+="\r\n";
        if (ReturnString.length+RowString.length>=200) {
          SendFunc(ReturnString);
          ReturnString="";
        }
        /*
        if (ReturnString.length==0) {
        ReturnString="(AD)\r\n";
      }
      */
      ReturnString+=RowString;
    }
    if (ReturnString.length>0) {
      SendFunc(ReturnString);
      ReturnString="";
    }
  });
}
};
