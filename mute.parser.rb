class Mute

	def initialize(textData)
		@textData = textData
		@memory = {}
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
		name = mute_line.to_s.scan(/[a-z0-9.#]+/i)[0]
		oper = mute_line.to_s.scan(/\{(.*?)\}/)
		cond = mute_line.to_s.scan(/\((.*?)\)/)
		sets = mute_line.gsub(/\{[^()]*\}/,"").to_s.scan(/\[(.*?)\]/)

		# 1. solver
		if cond.length > 0 && solver(name,cond) == 0 then return "" end

		# 2. setter		
		if sets.length > 0
			memSave(name,setter(name,sets))
		end

		# 3. render
		if oper.length > 0
			lineParser(oper[0][0])
		end


		return ""

	end

	def preview

		html = ""
		lineCount = 1;
		@textData.split("\n").each do |k,v|
			html += "<span style='color:#555'>"+lineCount.to_s+"</span> "+k+"<br />"
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