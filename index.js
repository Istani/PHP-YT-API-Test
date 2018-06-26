/* Checking Example File for New Data! */
var config = require('dotenv').config();
var fs = require('fs');
var config_example = "";
if (fs.existsSync("./.env")) {
    for (var attributename in config.parsed) {
        config_example += attributename + "=\r\n";
    }
    fs.writeFileSync('./.env.example', config_example);
} else {
    //fs.copyFileSync("./.env.example", ".env");
    console.log("Update .env Files first!");
    process.exit(1);
}
/* Example File Finish */

var async = require('async');
const db = require('./db.js');
var login = require("./models/login.js");
var data = {};
async.parallel([
    function (callback) {
        db.query("SELECT 1 as ONE", {}, function (err, results) {
            if (err) return callback(err);
            data.table1 = results;
            callback();
        });
    },
    function (callback) {
        db.query("SELECT 2 as TWO", {}, function (err, results) {
            if (err) return callback(err);
            data.table2 = results;
            callback();
        });
    },
], function (err) {
    if (err) console.log(err);
    db.end();
    console.log(data);
});

/* Beispiel SQL */


/* Webserver */
var express = require('express');
var exphbs = require('express-handlebars');
var i18n = require("i18n");

var hbs = exphbs.create({
    helpers: {
        i18n: function (key, options) {
            var temp_data = {}
            temp_data.data = options;
            //var result = i18n.__(key, temp_data);
            var result = i18n.__(key, options);
            result = result.split("[[").join("{{");
            result = result.split("]]").join("}}");
            result = hbs.handlebars.escapeExpression(result);
            result = hbs.handlebars.compile(result);   // Dann ist der String leer!
            result = result(temp_data);
            return result;
        }
    },
    defaultLayout: 'main',
    extname: '.hbs'
});

var app = express();

app.engine('.hbs', hbs.engine);
app.set('view engine', '.hbs');


i18n.configure({
    defaultLocale: 'de',
    cookie: 'syth_language',
    directory: './locales',
    queryParameter: 'lang',
    extension: '.json'
});
app.use(i18n.init);


app.get('/', function (req, res) {
    var temp_data = {}
    temp_data.user = { name: "Test" };

    res.render('home', {
        data: temp_data
    });
});

// route for handling 404 requests(unavailable routes)
app.use(function (req, res, next) {
    fs.readFile(__dirname + '/www/' + req.url, function (err, data) {
        if (err) {
            console.log("Datei nicht gefunden! " + __dirname + '/www' + req.url);
            res.render('404');
            return;
        }
        res.write(data);
        return res.end();
    });
});

app.listen(3000, () => console.log('Webinterface running!'));