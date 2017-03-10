info:
	@echo "Usage: make install|test|debug|doc"

# pass any target to composer
$(MAKECMDGOALS):
	composer $(MAKECMDGOALS)
