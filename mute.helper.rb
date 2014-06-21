class Mute

	def memlink index

		if index.include? "."

		elsif @memory[index]
			return @memory[index]
		end
		return index

	end

end