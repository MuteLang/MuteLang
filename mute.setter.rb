class Mute

	def setter name,sets

		sets.each do |k,v|
			return setterSet(k)
		end

	end

	def setterSet set

		operator = set.gsub(/[0-9a-z.]/i, '')

		if operator != ""
			return setterOperator(set,operator)
		else
			return setterSimple(set)
		end

	end

	def setterSimple set
		return memlink(set)
	end

	
	def setterOperator set,operator

		val1 = memlink( set.split(operator)[0] )
		val2 = memlink( set.split(operator)[1] )

		if val1.to_i != 0 then val1 = val1.to_i end
		if val2.to_i != 0 then val2 = val2.to_i end

		if set.include? "," then
			fixedReturn = Array.new
			set.split(",").each do |k,v|
				fixedReturn.push(setterSet(k))
			end
			return fixedReturn
		end

		if operator == "+" then return val1 + val2 end
		if operator == "-" then return val1 - val2 end
		if operator == "/" then return val1 / val2 end
		if operator == "*" then return val1 * val2 end

		return "multi"
	end

end