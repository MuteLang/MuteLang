class Mute

	def setter sets

		sets.each do |k,v|
			return setterSet(k)
		end

	end

	def setterSet set

		operator = set.gsub(/[0-9a-z.]/i, '')

		if operator != ""
			return setterOperator(set)
		else
			return setterSimple(set)
		end

	end

	def setterSimple set
		return memlink(set)
	end

	
	def setterOperator set
		
		if set.include? "," then return set.split(",") end
		if set.include? "+" then return operatorAdd(set) end

		return "multi"
	end

	def operatorAdd set

		accessor1 = set.split("+")[0]
		accessor2 = set.split("+")[1]

		accessor1 = memlink(accessor1)
		accessor2 = memlink(accessor2)

		if accessor1.to_i != 0 then accessor1 = accessor1.to_i end
		if accessor2.to_i != 0 then accessor2 = accessor2.to_i end

		return accessor1 + accessor2
	end

end