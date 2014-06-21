
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

puts "<pre>"
puts "=================<br />"
puts mute.preview.to_s+""
puts "=================<br />"
puts "</pre>"

puts "<pre>"
puts "=================<br />"
puts mute.output.to_s+""
puts "=================<br />"
puts "</pre>"

puts "<pre>"
puts "=================<br />"
puts mute.memory.to_s+""
puts "=================<br />"
puts "</pre>"