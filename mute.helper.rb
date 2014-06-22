class Mute

	def memlink index

		if @memory[index]
			return @memory[index]
		end

		if index.to_s.include? "."

			k = index.split(".")[0]
			v = index.split(".")[1]
			e = index.split(".")[2]

			v = memlink(v)

			if @memory[k].class == Hash
				if @memory[k][v].class == Hash
					if @memory[k][v][e.to_i] then return @memory[k][v][e.to_i] end
					if @memory[k][v][e] then return @memory[k][v][e.to_i] end
				end
				if @memory[k][v] then return @memory[k][v] end
			elsif @memory[k].class == Array
				if @memory[k][v.to_i] then return @memory[k][v.to_i] end
			end

		end

		return index

	end

	def memSave name,data

		if name.include? "."

			k = name.split(".")[0]
			v = name.split(".")[1]

			if @memory[k].class != Hash && @memory[k].class != Array
				@memory[k] = {}
			end
			if v.to_i > 0
				@memory[k][v.to_i] = data
			else
				@memory[k][v] = data
			end

		else
			@memory[name] = data
		end

	end

end