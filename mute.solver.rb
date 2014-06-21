class Mute

	def solver name,conditions
		
		conditions.each do |condition,v|
			if lineSolver(name,condition) == 0 then return 0 end
		end

		return 1

	end

	def lineSolver name,condition

		operator = condition.gsub(/[0-9a-z.]/i, '')

		if operator != ""
			return operationSolver(name,condition) 
		end

		if condition.to_i != 0 && condition != 0 
			return integerSolver(name,condition)
		end

	end

	def operationSolver name,condition

	end

	def integerSolver name,condition

		if @memory[name].to_i == condition.to_i
			return 1
		else
			return 0
		end

	end

end