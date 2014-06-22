class Mute

	def memlink index

		if @memory[index]
			return @memory[index]
		end

		if index.to_s.include? "."

			k = index.split(".")[0]
			v = index.split(".")[1]
			e = index.split(".")[2]

			if @memory[k].class == Hash
				if @memory[k][v].class == Hash
					if @memory[k][v][e.to_i] then return @memory[k][v][e.to_i] end
					if @memory[k][v][e] then return @memory[k][v][e.to_i] end
				end
				if @memory[k][v.to_i] then return @memory[k][v.to_i] end
				if @memory[k][v] then return @memory[k][v] end
			end

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