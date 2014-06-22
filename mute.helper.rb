class Mute

	def memlink index


		if index.include? "."
			return @memory[index.split(".")[0]][index.split(".")[1]]
		elsif @memory[index]
			return @memory[index]
		end
		return index

	end

	def memSave name,data

		if name.include? "."
			if @memory[name.split(".")[0]].class != Hash
				@memory[name.split(".")[0]] = {}
			end
			@memory[name.split(".")[0]][name.split(".")[1]] = data
		else
			@memory[name] = data
		end

	end

end