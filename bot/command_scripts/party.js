var self = module.exports = {
  execute: function (msg) {
    var amount=0;
    var tmp_para=msg.content.split(" ");
    if (typeof para[1]==undefiend) {
      amount=1;
    } else {
      if (parseInt(para[1]>0) {
        amount=parseInt(para[1]);
      }
    }
    self.execute_repeat(msg, amount);
    
    
  },
  execute_repeat:function (msg, amount) {
    msg.channel.sendTTSMessage("PARTY @everyone");
    amount=amount-1;
    if (amount>0) {
      setTimeout(self.execute_repeat(msg, amount), 1000);
    }
  };
};
