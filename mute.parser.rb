class Mute

	def initialize(textData)
		@textData = textData
		@memory = {}
		@memory["a"] = 5
	end

	def parser

		html = ""
		@textData.split("\n").each do |k,v|
			html += lineParser(k)
		end
		return html

	end

	def lineParser mute_line

		# memoryname
		name = mute_line.scan(/[a-z0-9#]+/i)[0]
		cond = mute_line.scan(/\((.*?)\)/)
		sets = mute_line.scan(/\[(.*?)\]/)
		oper = mute_line.scan(/\{(.*?)\}/)

		# 1. solver
		if solver(name,cond) == 0 then return "" end

		# 2. setter
		if sets.length > 0 then	@memory[name] = setter(sets) end

		


		return ""

	end

	def preview

		html = ""
		lineCount = 1;
		@textData.split("\n").each do |k,v|
			html += lineCount.to_s+" : "+k+"<br />"
			lineCount += 1
		end

		return html

	end

	def memory
		return @memory.to_s
	end

	def output
		return parser.to_s
	end

end