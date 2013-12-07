
require "tool.mute.rb"

prefill = {"title" => "demo"}
muteInitiate(prefill)

html = "
<p>Mute, or mutescript, is a small experimental programming language with single line functions inspired by <a href=\"http://en.wikipedia.org/wiki/APL_(programming_language)\">APL</a>. 
This is just a draft, and should grow into an actual documentation shortly. </p>
page title: {mute}"+'

if(mute.title=demo){"@",mute.title}

'+"{/mute}
<p>Mute lives in XXIIVV as a powerful templating language to display dynamic data from static content.</p>"

puts muteParseFromBlock(html)