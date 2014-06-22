class Mute

	def solver name,conditions

		conditions.each do |condition,v|
			if lineSolver(name,condition) == 0 then return 0 end
		end

		return 1

	end

	def lineSolver name,condition

		operator = condition.gsub(/[0-9a-z.]/i, '').lstrip.rstrip
		val1 = memlink(condition.split(operator)[0])
		val2 = memlink(condition.split(operator)[1])

		# Simple mode
		if !operator
			if memlink(name).to_i == condition.to_i then return 1 end
			if memlink(name).to_i == memlink(condition).to_i then return 1 end
		end

		# Operator mode
		if operator == "<" && val1.to_i < val2.to_i then return 1 end
		if operator == ">" && val1.to_i > val2.to_i then return 1 end
		if operator == "=" && val1.to_i ==val2.to_i then return 1 end

		return 0

	end

	def operationSolver name,condition
		return "wip"
	end

	def integerSolver name,condition

		

	end

end