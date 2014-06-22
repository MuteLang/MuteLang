class Mute

	def solver name,conditions

		conditions.each do |condition,v|
			if lineSolver(name,condition) == 0 then return 0 end
		end

		return 1

	end

	def lineSolver name,condition

		operator = condition.gsub(/[0-9a-z.]/i, '')

		if memlink(name).to_i == condition.to_i
			return 1
		elsif memlink(name).to_i == memlink(condition).to_i 
			return 1			
		end

		return 0

	end

	def operationSolver name,condition
		return "wip"
	end

	def integerSolver name,condition

		

	end

end