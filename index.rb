
load 'mute.helper.rb' 
load 'mute.parser.rb' 
load 'mute.solver.rb' 
load 'mute.setter.rb' 
load 'mute.render.rb' 

file = File.new("mute.txt", "r")
content = ""
while (line = file.gets)
    content += line
end
file.close

mute = Mute.new(content)

puts "<body style='margin:0px;background:#111;color:#fff'><pre style='padding:100px; color:orange;'>
<span style='color:#555'>preview</span>

"+mute.preview.to_s+"

<span style='color:#555'>Output</span>

"+mute.output.to_s+"

<span style='color:#555'>Memory</span>

"+mute.memory.to_s+"

</pre></body>"