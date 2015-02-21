
/** article indexes **/
db.getCollection("article").ensureIndex({
  "_id": NumberInt(1)
},[
  
]);

/** article records **/
db.getCollection("article").insert({
  "_id": ObjectId("53970d9d3cb38129008b4567"),
  "content": "<pre><span style=\\\"font-size: 14px; \\\">\/ 需要支持callback参数,返回jsonp格式{<br\/>    \"imageUrl\": \"http:\/\/localhost\/ueditor\/php\/controller.php?action=uploadimage\",<br\/>    \"imagePath\": \"\/ueditor\/php\/\",<br\/>    \"imageFieldName\": \"upfile\",<br\/>    \"imageMaxSize\": 2048,<br\/>    \"imageAllowFiles\": [\".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\"]}<\/span><br\/><\/pre><p><img src=\\\"http:\/\/www.crab.cc\/static\/ueditor\/php\/upload1\/\/20140528\/1401285098120622.jpg\\\"\/><\/p>",
  "title": "图片测试",
  "author": "螃蟹在晨跑",
  "tag": "图片测试",
  "time": NumberInt(1401285434),
  "cate": "web",
  "url": "static\/data\/art\/1401285434881.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("53970db43cb3812c008b4567"),
  "content": "<pre><span style=\\\"font-size: 14px; \\\">\/ 需要支持callback参数,返回jsonp格式{<br\/>    \"imageUrl\": \"http:\/\/localhost\/ueditor\/php\/controller.php?action=uploadimage\",<br\/>    \"imagePath\": \"\/ueditor\/php\/\",<br\/>    \"imageFieldName\": \"upfile\",<br\/>    \"imageMaxSize\": 2048,<br\/>    \"imageAllowFiles\": [\".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\"]}<\/span><br\/><\/pre><p><img src=\\\"http:\/\/www.crab.cc\/static\/ueditor\/php\/upload1\/\/20140528\/1401285098120622.jpg\\\"\/><\/p>",
  "title": "图片测试",
  "author": "螃蟹在晨跑",
  "tag": "图片测试",
  "time": NumberInt(1401285434),
  "cate": "web",
  "url": "static\/data\/art\/1401285434881.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("53970e0f3cb3812a008b4567"),
  "content": "<pre><span style=\\\"font-size: 14px; \\\">\/ 需要支持callback参数,返回jsonp格式{<br\/>    \"imageUrl\": \"http:\/\/localhost\/ueditor\/php\/controller.php?action=uploadimage\",<br\/>    \"imagePath\": \"\/ueditor\/php\/\",<br\/>    \"imageFieldName\": \"upfile\",<br\/>    \"imageMaxSize\": 2048,<br\/>    \"imageAllowFiles\": [\".png\", \".jpg\", \".jpeg\", \".gif\", \".bmp\"]}<\/span><br\/><\/pre><p><img src=\\\"http:\/\/www.crab.cc\/static\/ueditor\/php\/upload1\/\/20140528\/1401285098120622.jpg\\\"\/><\/p>",
  "title": "图片测试",
  "author": "螃蟹在晨跑",
  "tag": "图片测试",
  "time": NumberInt(1401285434),
  "cate": "web",
  "url": "static\/data\/art\/1401285434881.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("5381e99abd03834808000029"),
  "content": "<p><span style=\\\"color: rgb(69, 69, 69); font-family: tahoma, helvetica, arial; font-size: 12px; line-height: 24px; background-color: rgb(252, 248, 233);\\\">很久没有静下心来写博客了，这段时间太浮躁了，浮躁得找不到路了。也不知上次写日记是什么时候，反正脑海里已经没有这么一回事了。现在的我，每天忙忙碌碌，可是晚上闭上眼的时候却不知道这一天里我到底做了什么，其实我什么都没做，像只没有脚的鸟在一个狭窄的笼子里，一直飞啊，一直飞啊，累了就一个跟头摔下去睡着了。这样的生活很没意思，我还是喜欢写代码，可是这段时间里，我没有项目，我在工作日记写到我花了中午休息的时间学习codeigniter，第二天主管就来问我，你为什么学习ci框架，以后我们的项目用的是tp，你应该多学习一下tp框架。这样的话总让人无奈，没有当过boss，自然不知道boss怎么想的，但是我想啊，如果有一天我当上了boss，我绝对不会去改变员工的意愿，兴趣。现在的我总会拿以前的话问自己，记忆里多少保存着一些过去的憧憬，对比现在，我更能明白自己。一直以来我最怕的人永远是我自己。在知乎上看到了一个关于精神分裂的提问，突然觉得我是有那么一点的分裂，在掌控范围内的分裂。毛爷爷说过，与天奋斗极乐无穷，而于我而言，与我奋斗极乐无穷。如果哪一天我战胜了自己，我想我的人生该到了坐在阳台上看变天夕阳红了。我依旧抽烟，我依旧没头没脑，但是从今天起我想一点点的改变，我也不知道能改变多少，但是改变一点总是好的。我只是一只咸鱼，我坐在篱笆上吹风。<\/span><\/p>",
  "title": "从今天开始",
  "author": "螃蟹在晨跑",
  "tag": " 博客 开始",
  "time": NumberInt(1401022874),
  "cate": "低声细语",
  "url": "static\/data\/art\/14010228741714.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("538292debd0383400800002b"),
  "content": "<pre class=\\\"brush:php;toolbar:false\\\">$extension&nbsp;=&nbsp;get_loaded_extensions();\r\nvar_dump($extension);\r\n$mongo&nbsp;=&nbsp;new&nbsp;mongo(&quot;mongodb:\/\/10.0.31.57:27017&quot;,array(&#39;connect&#39;=&gt;true));\r\n\/\/var_dump($mongo);\r\n$username&nbsp;=&nbsp;&#39;y5lX1vcv&#39;;\r\n$password&nbsp;=&nbsp;&#39;SZR1ypdXJ2mM&#39;;\r\n$db&nbsp;=&nbsp;$mongo-&gt;selectDb(&#39;wei713_mongo_fzmw1abj&#39;);\r\nvar_dump($db-&gt;authenticate&nbsp;($username&nbsp;&nbsp;,&nbsp;$password&nbsp;&nbsp;));\r\n$collection&nbsp;=&nbsp;$db-&gt;selectCollection(&#39;article&#39;);\r\nvar_dump($collection);\r\n&nbsp;$query&nbsp;=&nbsp;array(&#39;title&#39;=&gt;&#39;mongo&#39;,&#39;author&#39;=&gt;&#39;螃蟹在晨跑&#39;,&#39;time&#39;=&gt;51400000,&#39;clicknum&#39;=&gt;10,&#39;cate&#39;=&gt;&#39;code&#39;,\r\n\t&#39;content&#39;=&gt;&#39;今天天气不错，螃蟹要晨跑&#39;,&#39;keyword&#39;=&gt;array(&#39;weather&#39;=&gt;1,&#39;mongo&#39;=&gt;10,&#39;tag&#39;=&gt;100)\r\n);\r\n$collection-&gt;insert($query);&nbsp;\r\n$find&nbsp;=&nbsp;$collection-&gt;findOne();\r\nvar_dump($find);<\/pre><p><br\/><\/p>",
  "title": "mongo test",
  "author": "螃蟹在晨跑",
  "tag": "mongo test",
  "time": NumberInt(1401066206),
  "cate": "mongo",
  "url": "static\/data\/art\/14010662061441.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("53829f26bd0383140a000029"),
  "content": "<pre class=\\\"brush:php;toolbar:false\\\">public&nbsp;function&nbsp;getDetail($id){\r\n\t\t$query&nbsp;=&nbsp;array(&#39;_id&#39;=&gt;new&nbsp;MongoId($id));\r\n\t\t$data&nbsp;=&nbsp;array(&#39;$inc&#39;=&gt;array(&#39;clicknum&#39;=&gt;1));\r\n\t\treturn&nbsp;$this-&gt;HomeDao-&gt;findAndModify($query,&nbsp;$data);\r\n\t}\r\n\tpublic&nbsp;function&nbsp;commentDao(){\r\n\t\treturn&nbsp;&nbsp;$this-&gt;_dao-&gt;fDao(&#39;HomeComments&#39;,&nbsp;&#39;Home&#39;);\r\n\t\r\n\t}\r\n\tpublic&nbsp;function&nbsp;getComments($id){\r\n\t\t$_id&nbsp;=&nbsp;new&nbsp;mongoId($id);\r\n\t\t$comments&nbsp;=&nbsp;$this-&gt;commentDao();\r\n\t\t$query&nbsp;=&nbsp;array(&#39;artid&#39;=&gt;$_id);\r\n\t\treturn&nbsp;$comments-&gt;getAll($query);\r\n\t}\r\n\t<\/pre><p><br\/><\/p>",
  "title": "src test",
  "author": "螃蟹在晨跑",
  "tag": "src",
  "time": NumberInt(1401069350),
  "cate": "html",
  "url": "static\/data\/art\/14010693501779.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("53834622bd03830c0d000029"),
  "content": "<p style=\\\"margin-top: 0px; margin-bottom: 0px; padding: 0px; color: rgb(69, 69, 69); font-family: tahoma, helvetica, arial; font-size: 12px; line-height: 24px; white-space: normal; background-color: rgb(252, 248, 233); \\\">早上睁开眼的时候一丝光线从窗帘的缝隙间射入眼睛明晃晃的，舟山的早晨亮得颇早。朝四周环顾了一下，发现大家都没醒又想睡回去，可一点睡意也没有，于是便拿起手机玩起来。今天灰机要回家了，兽兽要去姨妈那里把毕业设计做完，而我得去趟医院了，耳鸣拖了有一段时间了，这些日子我倍受煎熬，夜，对我来说已经变得无比喧闹了，渐渐的我害怕天黑了，害怕被折磨得无法入睡的夜。大概八点多的时候兽兽伸了个懒腰打着长哈欠，我很细声的问，兽兽起床了吗？兽兽还在酝酿，而我索性放下手机起床。洗漱完后我便打开电脑，在百度上查了到舟山医院的路线。兽兽去了隔壁叫处男和老高他们，灰机从被窝露出头来说，那么早啊？我把饭卡和十块钱放在灰机桌上，我说饭卡你拿去耍，我欠你的钱你耍回去就好了，走的时候把饭卡放我桌上，我大概下午才会回来。出门的时候我把们轻轻带上，然后去食堂吃早餐，当我叫了一碟炒饭的时候才想起来饭卡留给灰机了，我尴尬的看着打菜的小姐，你等会罢，我没带饭卡。打菜的小姐很爽快的把饭给我，拿去吃吧，没关系的。我突的迟疑了一会，接过碟子连谢谢都忘记说了。在校门口坐了33路车，然后按着百度地图上说的在风景合院下了。拿着手机找了一会路，走着走着发现自己迷路了。问了两个大爷，在临城整整绕了一圈。走到老砌菜场的时候问了一个阿姨，阿姨指着前边的写字楼说，过了这写字楼后边的红绿灯就到了。我道了声谢，阿姨骑着电瓶车走了十来米又回过头来，我载你去吧，我也正好去舟山医院。我上了车，阿姨把我载道医院的对面说，你到了，还要到那边去。我又道了声谢，觉得暖暖的。走进医院门诊大楼，大楼前边的大石头上写着，医者仁心四个大字。我在一楼挂了号，然后按着指示到上楼去找医生。耳鼻喉科相较于其他科病人较少，我去的时候一个人也没有。医生是个女性。三十来岁，戴着口罩，认不出具体相貌，煞一看，很像教我们大学英语的何芳洁老师。医生让我先做检查，把很奇怪的仪器套在耳朵上，检测的小屋很安静，渐渐的耳朵开始响起喧闹的蝉噪声，听着耳套上的声音，周遭异常安静，耳脉上血液流动的声音开始逐渐明朗起来。做完检测，拿着报告给个医生看，医生拿着笔在 报告纸上对着奇怪异常的波型画了个圈，沉思了一会说，你的右耳确实有问题，你这属于突发性耳聋，你应该早点来医院的，拖太久了不好治疗。我咽了口痰，弱弱的问，这个能治好吗？医生拿起报告，这个不能确定，目前为止也没有确切的治疗方法，如果你早点来治疗的画就比较好治疗了。这样吧，你先吊几天盐水观察一段时间。我沉思了一会，望着窗外，突然觉得人生又变得更加艰辛了，闭上眼的那瞬间，感觉好世界好凌乱。我说治吧，不管能不能治得好，总得为人生负点责任。取了药然后去输液大厅等着吊盐水。正值中午医生开始接班，人群开始变得熙熙攘攘起来。在等待输液的一个小时里我给袁小姐发了条说说，我住院了。袁小姐很快回了我的信息，我忙着排队无暇顾及她。人群越来越多，我被挤在了三圈外。等了很久看到医生拿着叫我，我举在人群里群了手，医生说先打屁股。我坐在一米高的凳子上，医生拿着红色液体的针让我把衣服掀开，我解了皮带，正当我要把那戴了四年的皮带解开时医生说，不必了，把衣服拿起来就好。吊液的时候我伸出左右看着那位微胖的胡子把针管扎到手背的血管里，血液沿着管子流了出来。如今的我依然能感觉到一点疼，手背上那点痛处传到脑海却让我感到一丝安慰。我还没死，我只是失去了一部分的听力而已，也许我的人生会变得更加艰辛，但是如今的我已经破罐破摔的这么活了那么久。失眠的时候我总能想起搏击俱乐部里的诺顿。无法想象一个人几个月没能睡个安眠觉会是怎么一种感觉，看着他没有任何生机的眼神，那眼睛周围包裹着的眼袋肿得像个被人切了个口子的皮球，我想此刻换成我我也依然对生命没有任何希冀了，我也不会选择了了一生，睡不睡得着已经无所谓了。<\/p><p style=\\\"margin-top: 0px; margin-bottom: 0px; padding: 0px; color: rgb(69, 69, 69); font-family: tahoma, helvetica, arial; font-size: 12px; line-height: 24px; white-space: normal; background-color: rgb(252, 248, 233); \\\">我拿着三袋氯化钠吊液找了个位子坐下，让旁边的阿姨帮我把吊液挂起来。我回了袁小姐的信息，我说，我在打点滴。突发性耳聋，医生说可能治不好了。说这些话的时候我略微的坦然了。此时此刻我也没有任何奢求，我也早已在那漫长的排队的时间里做好了最坏的打算。如果治不好了那么我也无所谓了，失去了宁静的夜晚，我还有白天。此时此刻能让我觉得我还算幸运的脑海里唯一存在的只有一个不认识的人。不是史铁生，也不是张海迪，更不是海伦凯勒。在邻居的耳朵听来的故事，我想伯爵在城堡到底是怎样的一种矜持，那样绝强陪着痛苦活过了二十个年头。记得伯爵的最后一篇文章里有这么一句话，天堂未必在前方，但是地狱就在身后。我发条说说，天气静好，体液微凉。附上了一张吊针的照片，我不是在矫情，我是只觉得有点孤单，我想得到一点关心。一个来医院，没有告知任何人，家人也不知道，第一个知道的我病情的人是袁小姐。我和三妞说我在打点滴，三妞问我需要钱吗？我突然很想现在就回到家。一动不动坐在椅子上，王者周遭的陌生人，我第一次觉得自己那么渺小，渺小到感觉自己突然有一天莫名的消失了也不会有人知道吧。一贯以来我都是把心事藏在没人知道的角落。没有知道我在百度空间上写了这篇日记，我在百度空间上写的文字从不出现在qq里。我把悲伤独自饮鸩止渴，我那些不羁的言语只是我对没有向往生活里无恙的呐喊。<\/p><p style=\\\"margin-top: 0px; margin-bottom: 0px; padding: 0px; color: rgb(69, 69, 69); font-family: tahoma, helvetica, arial; font-size: 12px; line-height: 24px; white-space: normal; background-color: rgb(252, 248, 233); \\\">十月以来，我的生活异常消沉。就业和毕业的压力，让我异常消沉。当听到龙兄说，我挺佩服混混的，这些年依然坚持自己梦想。那一刻我眼泪渐渐滋润了双眼，我没有流泪，收起在眼里打转。如今的我已经没有什么梦想了，没有当初的狂妄，我不想征服世界，我现在连自己都没法改变。在v2ex上发了张贴，引来众人的狂喷。有人说我是炮嘴党，有人说我是在求同情。没有人知道我独自一人感到杭州参加同花顺的面试时候，坐了四个小时的车，一下车就吐了，结果等来一份C++的试卷，我不知有多心酸。回来的时候又吐了，我不没有眼高手低，我只想求一份脚踏实地的工作，做个简单的人，没想到会那么难。当我决定留在杭州的时候，我唯一想的就是能离袁小姐近一些，也许不再有可能会走到一块，我现在想做的只是不让以后的我后悔。毕业答辩的时候我在教室门口等了三个小时，我一个人在走廊吹着风的时候我在消防栓的旁边用钥匙写了三个袁小姐。如果将来有可能我希望袁小姐能看到。<\/p><p style=\\\"margin-top: 0px; margin-bottom: 0px; padding: 0px; color: rgb(69, 69, 69); font-family: tahoma, helvetica, arial; font-size: 12px; line-height: 24px; white-space: normal; background-color: rgb(252, 248, 233); \\\">从医院出来，我在乐购打电话给老高，我问，从乐购怎么坐车回学校啊。老高支吾了半天然后让我问叫兽，我转而问叫兽，叫兽说不知道。我在公交站牌转了圈，看到106路的终点站竟然是海洋学院，无奈的摇摇头。走在空旷的校园里，风呼呼的吹着，广播从远处传来，也许我应该庆幸自己还能听到声音。这样就足以让热泪满盈了。风呼呼的吹过，我只是不能听到一部分频率的声音而已，至少还有风声。我已再去考虑为什么要活着这样就扯不清的话题了。我想此刻我不要做的是以另一种姿态准备迎接我下一刻的人生，我想把人生过得简单一点，这样就满足了。最后引用程浩的那句话，“<span style=\\\"color: rgb(65, 66, 67); line-height: 27px; \\\">也许我们无法实现自己的梦想，但是我们已经为梦想流下了太多泪水。”<\/span><\/p><p><br\/><\/p>",
  "title": "背对阳光",
  "author": "螃蟹在晨跑",
  "tag": "阳光 阴影",
  "time": NumberInt(1401112098),
  "cate": "低声细语",
  "url": "static\/data\/art\/14011120981869.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("5385eb3abd0383c008000029"),
  "content": "<pre><span style=\\\"font-size: 14px; \\\">\/ 需要支持callback参数,返回jsonp格式{<br\/> &nbsp; &nbsp;&quot;imageUrl&quot;: &quot;http:\/\/localhost\/ueditor\/php\/controller.php?action=uploadimage&quot;,<br\/> &nbsp; &nbsp;&quot;imagePath&quot;: &quot;\/ueditor\/php\/&quot;,<br\/> &nbsp; &nbsp;&quot;imageFieldName&quot;: &quot;upfile&quot;,<br\/> &nbsp; &nbsp;&quot;imageMaxSize&quot;: 2048,<br\/> &nbsp; &nbsp;&quot;imageAllowFiles&quot;: [&quot;.png&quot;, &quot;.jpg&quot;, &quot;.jpeg&quot;, &quot;.gif&quot;, &quot;.bmp&quot;]}<\/span><br\/><\/pre><p><img src=\\\"http:\/\/www.crab.cc\/static\/ueditor\/php\/upload1\/\/20140528\/1401285098120622.jpg\\\"\/><\/p>",
  "title": "图片测试",
  "author": "螃蟹在晨跑",
  "tag": "图片测试",
  "time": NumberInt(1401285434),
  "cate": "web",
  "url": "static\/data\/art\/1401285434881.html"
});
db.getCollection("article").insert({
  "_id": ObjectId("53985afcbd03837c0900002a"),
  "type": "article",
  "content": "<p><span style=\\\"color: rgb(51, 51, 51); font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 20px; background-color: rgb(254, 254, 254);\\\">生活中我们总会被现实和理想的差距困惑，你渴望一份安逸的工作，但却工资不高，你希望交个女友，她什么都好，但感觉不对。你开始为了生存而工作，却忘记了自己是为什么想工作；你准备要结婚了，但放低了爱情在你心间的位置。有一天我听到了这样一句话：我们年轻时追逐的理想和现在努力奔波着的却是天壤之别，只因为时间的脚步从不停留。于是让我想把下面的文字分享给大家。<\/span><\/p>",
  "title": "突然想起理想这个词",
  "author": "螃蟹在晨跑",
  "tag": "理想，词",
  "time": NumberInt(1402493692),
  "cate": "低声细语",
  "url": "static\/data\/art\/14024936921795.html"
});
